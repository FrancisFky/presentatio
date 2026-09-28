<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/**
 * Les informations de l'ambassade telles que le site public les affiche.
 * Chaque réglage peut être vide (installation neuve) : on retombe alors sur
 * les coordonnées de l'ancien site plutôt que sur un trou dans la page.
 */
class Site
{
    public const DEFAULT_NAME = [
        'fr' => 'Ambassade de la République du Congo au Kenya',
        'en' => 'Embassy of the Republic of the Congo in Kenya',
    ];
    public const DEFAULT_ADDRESS = 'United Crescent, Gigiri, Nairobi, Kenya';
    public const DEFAULT_PHONE = '+254 707 786 276';
    public const DEFAULT_EMAIL = 'embacoken.diplomatic@gmail.com';

    /** Photos livrées avec le site, utilisées tant que l'admin n'en a pas choisi */
    public const DEFAULT_HERO = 'images/site/ambassade-2.jpg';
    public const DEFAULT_AMBASSADOR = 'images/site/ambassadeur.jpg';
    public const PAGE_IMAGES = [
        'about-congo' => 'images/site/drapeau-congo.jpg',
        'about-embassy' => 'images/site/ambassade-4.jpg',
        'invest-in-congo' => 'images/site/drapeaux-congo-kenya.jpg',
    ];

    /** Pages éditoriales du menu « À propos », dans l'ordre du menu */
    public const ABOUT_PAGES = ['about-congo', 'about-embassy', 'invest-in-congo'];

    public const SOCIALS = [
        'facebook' => ['icon' => 'ph-facebook-logo', 'label' => 'Facebook'],
        'x' => ['icon' => 'ph-x-logo', 'label' => 'X'],
        'instagram' => ['icon' => 'ph-instagram-logo', 'label' => 'Instagram'],
        'linkedin' => ['icon' => 'ph-linkedin-logo', 'label' => 'LinkedIn'],
        'youtube' => ['icon' => 'ph-youtube-logo', 'label' => 'YouTube'],
    ];

    public static function name(): string
    {
        $locale = app()->getLocale();

        return Setting::localized('site.name', $locale, self::DEFAULT_NAME[$locale] ?? self::DEFAULT_NAME[Locales::DEFAULT]);
    }

    public static function address(): string
    {
        return Setting::get('site.address', self::DEFAULT_ADDRESS);
    }

    /** Plusieurs numéros possibles, séparés par des virgules dans l'admin */
    public static function phones(): array
    {
        $phones = array_values(array_filter(array_map('trim', explode(',', (string) Setting::get('site.phone')))));

        return $phones ?: [self::DEFAULT_PHONE];
    }

    public static function email(): string
    {
        return Setting::get('site.email', self::DEFAULT_EMAIL);
    }

    public static function hours(): string
    {
        return Setting::localized('site.working_hours', default: __('site.contact.default_hours'));
    }

    /** Le lien de l'admin, sinon une recherche Google Maps sur l'adresse */
    public static function mapUrl(): string
    {
        return Setting::get('site.map_url', 'https://www.google.com/maps/search/?api=1&query=' . urlencode(self::address()));
    }

    /** Seulement les réseaux renseignés */
    public static function socials(): array
    {
        return collect(self::SOCIALS)
            ->map(fn ($social, $key) => $social + ['url' => Setting::get("site.{$key}")])
            ->filter(fn ($social) => filled($social['url']))
            ->all();
    }

    /** Contacts d'urgence renseignés ; la ligne d'urgence retombe sur le standard */
    public static function emergency(): array
    {
        return array_filter([
            'hotline' => Setting::get('contacts.hotline', self::phones()[0]),
            'whatsapp' => Setting::get('contacts.whatsapp'),
            'email' => Setting::get('contacts.email'),
            'duty_officer' => Setting::get('contacts.duty_officer'),
            'instructions' => Setting::localized('contacts.instructions'),
        ]);
    }

    /** Où arrivent les alertes « nouvelle demande » */
    public static function notificationEmail(): ?string
    {
        return Setting::get('site.notification_email') ?: Setting::get('site.email') ?: config('mail.from.address');
    }

    public static function logo(): string
    {
        return self::media(Setting::get('site.logo'), 'images/armoiries.svg');
    }

    public static function ambassadorPublished(): bool
    {
        return Setting::get('ambassador.published') !== '0';
    }

    /** URL d'un fichier du disque public, ou d'une image livrée avec le site */
    public static function media(?string $path, ?string $fallback = null): ?string
    {
        if (filled($path)) {
            return Storage::disk('public')->url($path);
        }

        return $fallback ? asset($fallback) : null;
    }

    /** « tel: » sans espaces ni tirets */
    public static function tel(string $phone): string
    {
        return 'tel:' . preg_replace('/[^\d+]/', '', $phone);
    }

    public static function whatsapp(string $phone): string
    {
        return 'https://wa.me/' . preg_replace('/\D/', '', $phone);
    }

    /**
     * La page courante dans une autre langue. Les modèles gardent la même
     * adresse (slug ou id) dans les deux langues : seul le préfixe change.
     * `$keep` : paramètres d'URL à conserver (tous si null).
     */
    public static function localizedUrl(string $locale, ?array $keep = null): string
    {
        $route = request()->route();
        $name = $route?->getName();

        if (! $name || ! str_starts_with($name, 'site.') || ! Route::has($name)) {
            return route('site.home', ['locale' => $locale]);
        }

        $query = $keep === null ? request()->query() : array_filter(request()->only($keep), 'filled');

        return route($name, [...$route->parameters(), 'locale' => $locale]) . ($query ? '?' . http_build_query($query) : '');
    }

    /** Paramètres qui changent vraiment le contenu (pour canonical et hreflang) */
    public const CANONICAL_QUERY = ['page', 'category', 'tab'];
}
