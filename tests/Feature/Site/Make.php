<?php

namespace Tests\Feature\Site;

use App\Models\Announcement;
use App\Models\Document;
use App\Models\Event;
use App\Models\Holiday;
use App\Models\News;
use App\Models\Page;
use App\Models\Service;
use Illuminate\Support\Str;

/** Contenus de test minimaux : publiés par défaut, à surcharger au besoin */
class Make
{
    public static function news(array $attributes = []): News
    {
        return News::create($attributes + [
            'slug' => 'article-' . Str::lower(Str::random(6)),
            'title_fr' => 'Visite officielle à Nairobi',
            'title_en' => 'Official visit to Nairobi',
            'excerpt_fr' => 'Résumé de la visite.',
            'body_fr' => '<p>Corps de l\'article.</p>',
            'published_on' => today()->subDay(),
            'status' => News::STATUS_PUBLISHED,
        ]);
    }

    public static function announcement(array $attributes = []): Announcement
    {
        return Announcement::create($attributes + [
            'title_fr' => 'Fermeture exceptionnelle',
            'title_en' => 'Exceptional closure',
            'body_fr' => '<p>L\'ambassade sera fermée.</p>',
            'priority' => 'normal',
            'published_on' => today()->subDay(),
            'status' => Announcement::STATUS_PUBLISHED,
        ]);
    }

    public static function service(array $attributes = []): Service
    {
        return Service::create($attributes + [
            'title_fr' => 'Délivrance de passeport',
            'title_en' => 'Passport issuance',
            'icon' => 'identification-card',
            'description_fr' => '<p>Demande ou renouvellement du passeport.</p>',
            'fees_fr' => '<p>100 USD</p>',
            'processing_time_fr' => '3 semaines',
            'status' => Service::STATUS_PUBLISHED,
        ]);
    }

    public static function event(array $attributes = []): Event
    {
        return Event::create($attributes + [
            'title_fr' => 'Fête de l\'indépendance',
            'title_en' => 'Independence Day',
            'venue_fr' => 'Résidence de l\'Ambassadeur',
            'starts_on' => today()->addWeek(),
            'starts_at' => '18:00:00',
            'status' => Event::STATUS_PUBLISHED,
        ]);
    }

    public static function page(string $slug, array $attributes = []): Page
    {
        return Page::create($attributes + [
            'slug' => $slug,
            'title_fr' => 'À propos du Congo',
            'title_en' => 'About Congo',
            'body_fr' => '<p>La République du Congo.</p>',
            'status' => Page::STATUS_PUBLISHED,
        ]);
    }

    public static function document(array $attributes = []): Document
    {
        return Document::create($attributes + [
            'title_fr' => 'Formulaire de visa',
            'title_en' => 'Visa form',
            'category' => 'Visas',
            'file_path' => 'documents/visa.pdf',
            'file_name' => 'formulaire-visa.pdf',
            'file_size' => 2048,
            'status' => Document::STATUS_PUBLISHED,
        ]);
    }

    public static function holiday(string $date, array $attributes = []): Holiday
    {
        return Holiday::create($attributes + [
            'name_fr' => 'Fête nationale',
            'name_en' => 'National Day',
            'date' => $date,
            'status' => Holiday::STATUS_PUBLISHED,
        ]);
    }
}
