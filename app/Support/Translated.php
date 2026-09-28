<?php

namespace App\Support;

/**
 * Règles de validation d'un champ traduit : les mêmes règles pour chaque
 * langue ; « obligatoire » ne vaut que pour le français.
 *
 *   ...Translated::rules('title', ['string', 'max:255'], required: true)
 *   → ['title_fr' => ['required', 'string', 'max:255'], 'title_en' => ['nullable', 'string', 'max:255']]
 */
class Translated
{
    public static function rules(string $field, array $rules = ['string'], bool $required = false): array
    {
        $result = [];

        foreach (Locales::SUPPORTED as $locale) {
            $presence = $required && $locale === Locales::DEFAULT ? 'required' : 'nullable';
            $result["{$field}_{$locale}"] = [$presence, ...$rules];
        }

        return $result;
    }

    /** Nettoie le HTML des champs mis en forme, dans chaque langue */
    public static function cleanRich(array $data, array $fields): array
    {
        foreach ($fields as $field) {
            foreach (Locales::SUPPORTED as $locale) {
                $key = "{$field}_{$locale}";
                if (array_key_exists($key, $data)) {
                    $data[$key] = RichText::clean($data[$key]);
                }
            }
        }

        return $data;
    }

    /** Les noms de colonnes d'un champ : ['title_fr', 'title_en'] */
    public static function columns(string $field): array
    {
        return array_map(fn ($locale) => "{$field}_{$locale}", Locales::SUPPORTED);
    }
}
