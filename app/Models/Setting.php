<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Once;

/**
 * Réglages clé/valeur modifiables depuis l'admin (`site.address`, `home.hero_title_fr`…).
 * Toute la table est lue en une requête et gardée en cache : une page du site
 * en consulte une trentaine.
 */
class Setting extends Model
{
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    const CACHE_KEY = 'settings:all';

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = self::cached()[$key] ?? null;

        return filled($value) ? $value : $default;
    }

    public static function set(string $key, mixed $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
        self::flush();
    }

    /** Les valeurs d'un groupe, sans le préfixe : group('home')['hero_title_fr'] */
    public static function group(string $group): array
    {
        $prefix = $group . '.';

        return collect(self::cached())
            ->filter(fn ($value, $key) => str_starts_with($key, $prefix))
            ->mapWithKeys(fn ($value, $key) => [substr($key, strlen($prefix)) => $value])
            ->all();
    }

    /** Valeur traduite d'un réglage, avec repli sur l'autre langue */
    public static function localized(string $key, ?string $locale = null, mixed $default = null): mixed
    {
        $locale ??= app()->getLocale();

        foreach (\App\Support\Locales::fallbackChain($locale) as $candidate) {
            $value = self::get("{$key}_{$candidate}");
            if (filled($value)) {
                return $value;
            }
        }

        return $default;
    }

    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
        Once::flush();
    }

    /** Lu une fois par requête */
    private static function cached(): array
    {
        return once(fn () => self::loadAll());
    }

    private static function loadAll(): array
    {
        $cached = Cache::get(self::CACHE_KEY);
        if (is_array($cached)) {
            return $cached;
        }

        // Avant la première migration (installation), le site doit quand même s'afficher
        if (!Schema::hasTable('settings')) {
            return [];
        }

        $all = self::query()->pluck('value', 'key')->all();
        Cache::forever(self::CACHE_KEY, $all);

        return $all;
    }
}
