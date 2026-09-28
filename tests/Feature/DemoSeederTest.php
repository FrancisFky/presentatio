<?php

use App\Models\Event;
use App\Models\News;
use App\Models\Photo;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DemoSeeder;
use Illuminate\Support\Facades\Storage;

it('remplit le site de démonstration, et chaque page publique s\'affiche', function () {
    Storage::fake('public');
    $this->seed([DatabaseSeeder::class, DemoSeeder::class]);

    expect(News::visible()->count())->toBeGreaterThan(5)
        ->and(Event::upcoming()->count())->toBeGreaterThan(2)
        ->and(Photo::where('is_featured', true)->count())->toBeGreaterThan(3);

    foreach (['/fr', '/en', '/fr/actualites', '/fr/evenements', '/fr/services', '/fr/galerie', '/fr/documents', '/fr/about-congo', '/fr/ambassadeur', '/fr/rendez-vous', '/fr/contact'] as $url) {
        $this->get($url)->assertOk();
    }
    $this->get(route('site.news.show', ['locale' => 'en', 'news' => News::visible()->first()]))->assertOk();
});
