<?php

use App\Models\News;
use Tests\Feature\Site\Make;

test('le plan du site liste les pages publiées dans les deux langues', function () {
    $news = Make::news();
    $draft = Make::news(['status' => News::STATUS_DRAFT]);
    Make::page('about-embassy');

    $response = $this->get('/sitemap.xml')->assertOk();

    expect($response->headers->get('Content-Type'))->toContain('application/xml');
    $response
        ->assertSee('<?xml version="1.0" encoding="UTF-8"?>', false)
        ->assertSee('<loc>' . url('/fr') . '</loc>', false)
        ->assertSee('<loc>' . url("/en/actualites/{$news->slug}") . '</loc>', false)
        ->assertSee('hreflang="x-default"', false)
        ->assertSee(url('/fr/about-embassy'), false)
        ->assertDontSee($draft->slug);

    // XML bien formé
    expect(simplexml_load_string($response->getContent()))->not->toBeFalse();
});

test('robots.txt ferme l’admin et annonce le plan du site', function () {
    $this->get('/robots.txt')
        ->assertOk()
        ->assertSee('Disallow: /admin')
        ->assertSee('Sitemap: ' . url('/sitemap.xml'));
});
