<?php

namespace App\Support;

/**
 * Les réglages que l'admin peut modifier, groupe par groupe. Chaque groupe a
 * sa page dans l'admin, générée à partir de cette liste.
 *
 * Types : text, textarea, rich (éditeur), url, email, image, boolean.
 * `translated` : un champ par langue (`hero_title_fr`, `hero_title_en`).
 */
class SettingGroups
{
    public static function all(): array
    {
        return [
            'site' => [
                'title' => 'Paramètres du site',
                'icon' => 'ph-sliders',
                'intro' => "Coordonnées de l'ambassade, réseaux sociaux et référencement. Elles s'affichent dans l'en-tête, le pied de page et la page Contact.",
                'sensitive' => true,
                'sections' => [
                    'Identité' => [
                        'name' => ['type' => 'text', 'label' => "Nom de l'ambassade", 'translated' => true],
                        'logo' => ['type' => 'image', 'label' => 'Armoiries / logo', 'help' => 'PNG ou WebP carré, fond transparent de préférence.'],
                    ],
                    'Coordonnées' => [
                        'address' => ['type' => 'textarea', 'label' => 'Adresse postale'],
                        'phone' => ['type' => 'text', 'label' => 'Téléphone(s)', 'help' => 'Séparez plusieurs numéros par une virgule.'],
                        'email' => ['type' => 'email', 'label' => 'E-mail public'],
                        'notification_email' => ['type' => 'email', 'label' => 'E-mail qui reçoit les demandes', 'help' => 'Rendez-vous et messages du site y sont signalés. Vide : e-mail public.'],
                        'working_hours' => ['type' => 'text', 'label' => "Heures d'ouverture", 'translated' => true],
                        'map_url' => ['type' => 'url', 'label' => 'Lien Google Maps'],
                    ],
                    'Réseaux sociaux' => [
                        'facebook' => ['type' => 'url', 'label' => 'Facebook'],
                        'x' => ['type' => 'url', 'label' => 'X (Twitter)'],
                        'instagram' => ['type' => 'url', 'label' => 'Instagram'],
                        'linkedin' => ['type' => 'url', 'label' => 'LinkedIn'],
                        'youtube' => ['type' => 'url', 'label' => 'YouTube'],
                    ],
                    'Pied de page et référencement' => [
                        'footer' => ['type' => 'textarea', 'label' => 'Texte du pied de page', 'translated' => true],
                        'seo_description' => ['type' => 'textarea', 'label' => 'Description pour Google', 'translated' => true, 'help' => '150 caractères environ.'],
                    ],
                ],
            ],

            'home' => [
                'title' => "Page d'accueil",
                'icon' => 'ph-house',
                'intro' => "Le bandeau d'ouverture et les textes de présentation de l'accueil. Les actualités, services et événements s'y ajoutent d'eux-mêmes.",
                'sections' => [
                    'Bandeau' => [
                        'hero_title' => ['type' => 'text', 'label' => 'Titre', 'translated' => true],
                        'hero_subtitle' => ['type' => 'textarea', 'label' => 'Sous-titre', 'translated' => true],
                        'hero_image' => ['type' => 'image', 'label' => 'Photo de fond', 'help' => 'Paysage, au moins 1920 px de large.', 'max_side' => 2400],
                    ],
                    'Présentation' => [
                        'welcome' => ['type' => 'textarea', 'label' => 'Mot de bienvenue', 'translated' => true],
                        'mission' => ['type' => 'textarea', 'label' => 'Mission', 'translated' => true],
                        'vision' => ['type' => 'textarea', 'label' => 'Vision', 'translated' => true],
                        'objectives' => ['type' => 'textarea', 'label' => 'Objectifs', 'translated' => true],
                    ],
                ],
            ],

            'ambassador' => [
                'title' => 'Ambassadeur',
                'icon' => 'ph-user-focus',
                'intro' => "Le mot de l'ambassadeur, affiché sur l'accueil et sur sa page.",
                'sections' => [
                    'Portrait' => [
                        'name' => ['type' => 'text', 'label' => 'Nom complet'],
                        'position' => ['type' => 'text', 'label' => 'Fonction', 'translated' => true],
                        'photo' => ['type' => 'image', 'label' => 'Photo officielle', 'max_side' => 1000],
                        'published' => ['type' => 'boolean', 'label' => "Afficher le mot de l'ambassadeur sur le site"],
                    ],
                    'Textes' => [
                        'message' => ['type' => 'textarea', 'label' => 'Mot de bienvenue (court, pour l\'accueil)', 'translated' => true],
                        'biography' => ['type' => 'rich', 'label' => 'Biographie', 'translated' => true],
                        'signature' => ['type' => 'text', 'label' => 'Signature'],
                    ],
                ],
            ],

            'contacts' => [
                'title' => "Contacts d'urgence",
                'icon' => 'ph-first-aid-kit',
                'intro' => "Joignables en dehors des heures d'ouverture : ressortissants en difficulté, décès, arrestation, perte de passeport.",
                'sections' => [
                    'Joindre l\'ambassade en urgence' => [
                        'hotline' => ['type' => 'text', 'label' => "Ligne d'urgence"],
                        'whatsapp' => ['type' => 'text', 'label' => 'WhatsApp'],
                        'email' => ['type' => 'email', 'label' => 'E-mail'],
                        'duty_officer' => ['type' => 'text', 'label' => 'Agent de permanence'],
                        'instructions' => ['type' => 'textarea', 'label' => 'Consignes', 'translated' => true],
                    ],
                ],
            ],
        ];
    }

    public static function get(string $group): ?array
    {
        return self::all()[$group] ?? null;
    }

    /**
     * Les champs à plat, un par langue pour les champs traduits :
     * ['hero_title_fr' => [...], 'hero_title_en' => [...], 'hero_image' => [...]]
     */
    public static function fields(string $group): array
    {
        $fields = [];

        foreach (self::get($group)['sections'] ?? [] as $section) {
            foreach ($section as $name => $field) {
                if ($field['translated'] ?? false) {
                    foreach (Locales::SUPPORTED as $locale) {
                        $fields["{$name}_{$locale}"] = $field + ['locale' => $locale, 'base' => $name];
                    }
                } else {
                    $fields[$name] = $field + ['base' => $name];
                }
            }
        }

        return $fields;
    }
}
