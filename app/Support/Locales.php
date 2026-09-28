<?php

namespace App\Support;

/** Le site est en français et en anglais ; le français est la langue par défaut. */
class Locales
{
    public const SUPPORTED = ['fr', 'en'];
    public const DEFAULT = 'fr';

    public const LABELS = ['fr' => 'Français', 'en' => 'English'];

    public static function isSupported(?string $locale): bool
    {
        return in_array($locale, self::SUPPORTED, true);
    }

    /** La langue demandée d'abord, puis les autres */
    public static function fallbackChain(string $locale): array
    {
        return array_values(array_unique([$locale, ...self::SUPPORTED]));
    }

    public static function other(string $locale): string
    {
        return $locale === 'fr' ? 'en' : 'fr';
    }
}
