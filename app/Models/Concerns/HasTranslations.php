<?php

namespace App\Models\Concerns;

use App\Support\Locales;

/**
 * Les champs traduits sont stockés en colonnes jumelles (`title_fr`, `title_en`).
 * `$model->t('title')` renvoie la langue demandée, sinon l'autre : une page
 * encore non traduite reste lisible plutôt que vide.
 */
trait HasTranslations
{
    public function t(string $field, ?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();

        foreach (Locales::fallbackChain($locale) as $candidate) {
            $value = $this->getAttribute("{$field}_{$candidate}");
            if (filled($value) && filled(trim(strip_tags((string) $value)))) {
                return $value;
            }
        }

        return null;
    }

    /** Vrai si le champ manque dans une des langues (repéré dans l'admin) */
    public function missingTranslation(string $field): bool
    {
        return collect(Locales::SUPPORTED)->contains(fn ($locale) => blank($this->getAttribute("{$field}_{$locale}")));
    }
}
