<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Laravel\Facades\Image;

/**
 * Photos envoyées par les apps : redimensionnées, converties en WebP, stockées
 * sur le disque public. L'ancienne photo est effacée quand on la remplace.
 *
 * `disk` permet de sortir du public ce qui n'a rien à y faire : une pièce
 * d'identité va sur « local », servie par le back-office après contrôle, et
 * jamais par une URL qu'il suffirait de partager.
 */
class Images
{
    /** Mémoire demandée pour décoder une grande photo (hébergement mutualisé : 128 Mo par défaut) */
    const WORKING_MEMORY = 256 * 1024 * 1024;

    public static function store(UploadedFile $file, string $folder, int $maxSide = 1280, ?string $replace = null, string $disk = 'public'): string
    {
        self::ensureMemory(self::WORKING_MEMORY);

        $path = $folder . '/' . date('Y/m') . '/' . Str::lower(Str::random(24)) . '.webp';

        $webp = Image::decodePath($file->getRealPath())
            ->scaleDown(width: $maxSide, height: $maxSide)
            ->encode(new WebpEncoder(quality: 82));
        Storage::disk($disk)->put($path, (string) $webp);

        if ($replace) {
            self::delete($replace, $disk);
        }

        return $path;
    }

    /** Relève temporairement memory_limit si l'hébergeur le permet */
    private static function ensureMemory(int $bytes): void
    {
        $limit = trim((string) ini_get('memory_limit'));

        if ($limit === '-1') {
            return;
        }

        $current = (int) $limit;
        $unit = strtolower(substr($limit, -1));
        $current *= match ($unit) {
            'g' => 1024 ** 3,
            'm' => 1024 ** 2,
            'k' => 1024,
            default => 1,
        };

        if ($current < $bytes) {
            @ini_set('memory_limit', (string) $bytes);
        }
    }

    public static function delete(?string $path, string $disk = 'public'): void
    {
        if ($path && Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }
}
