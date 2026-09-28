<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\NewsCategory;
use App\Models\Page;
use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * Ce dont le site a besoin pour s'afficher correctement dès l'installation.
 * Rejouable : rien n'est écrasé.
 */
class BaseContentSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            'about-congo' => ['À propos du Congo', 'About Congo'],
            'about-embassy' => ["À propos de l'Ambassade", 'About the Embassy'],
            'invest-in-congo' => ['Investir au Congo', 'Invest in Congo'],
        ];
        foreach ($pages as $slug => [$fr, $en]) {
            Page::firstOrCreate(['slug' => $slug], ['title_fr' => $fr, 'title_en' => $en, 'status' => Page::STATUS_DRAFT]);
        }

        if (NewsCategory::doesntExist()) {
            foreach ([
                ["Vie de l'Ambassade", 'Embassy News'],
                ['Services consulaires', 'Consular Services'],
                ['Relations diplomatiques', 'Diplomatic Relations'],
                ['Événements', 'Events'],
                ['Communauté', 'Community'],
                ['Information publique', 'Public Information'],
            ] as [$fr, $en]) {
                NewsCategory::create(['name_fr' => $fr, 'name_en' => $en]);
            }
        }

        if (Album::doesntExist()) {
            foreach ([
                ['Ambassade et chancellerie', 'Embassy & Chancery'],
                ['Événements diplomatiques', 'Diplomatic Events'],
                ['Services consulaires', 'Consular Services'],
                ['Rencontres officielles', 'Official Meetings'],
                ['Célébrations nationales', 'National Celebrations'],
                ['Communauté et diaspora', 'Community & Diaspora'],
            ] as [$fr, $en]) {
                Album::create(['name_fr' => $fr, 'name_en' => $en]);
            }
        }

        // Coordonnées reprises de l'ancien site, modifiables dans Paramètres
        $defaults = [
            'site.name_fr' => 'Ambassade de la République du Congo au Kenya',
            'site.name_en' => 'Embassy of the Republic of the Congo in Kenya',
            'site.address' => 'United Crescent, Gigiri, Nairobi, Kenya',
            'site.phone' => '+254 707 786 276',
            'site.email' => 'embacoken.diplomatic@gmail.com',
            'site.working_hours_fr' => 'Du lundi au vendredi, de 9 h à 17 h',
            'site.working_hours_en' => 'Monday to Friday, 9:00 am – 5:00 pm',
            'ambassador.published' => '1',
        ];
        foreach ($defaults as $key => $value) {
            if (Setting::find($key) === null) {
                Setting::set($key, $value);
            }
        }
    }
}
