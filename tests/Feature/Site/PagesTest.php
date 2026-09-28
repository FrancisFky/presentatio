<?php

use App\Models\Album;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Page;
use App\Models\Photo;
use App\Models\Setting;
use Tests\Feature\Site\Make;

test('la racine redirige vers la langue du navigateur, français par défaut', function () {
    // Sans en-tête (Symfony en ajoute un anglais par défaut aux requêtes de test)
    $this->get('/', ['Accept-Language' => ''])->assertRedirect('/fr');
    $this->get('/', ['Accept-Language' => 'en-GB,en;q=0.9'])->assertRedirect('/en');
    $this->get('/', ['Accept-Language' => 'de-DE'])->assertRedirect('/fr');
});

test("l'accueil s'affiche avec une base vide", function () {
    $this->get('/fr')
        ->assertOk()
        ->assertSee('<html lang="fr"', false)
        ->assertSee("Bienvenue à l'Ambassade de la République du Congo au Kenya")
        ->assertSee('+254 707 786 276')
        ->assertSee('embacoken.diplomatic@gmail.com');

    $this->get('/en')->assertOk()->assertSee('<html lang="en"', false)->assertSee('Book an appointment');
});

test("l'accueil montre les contenus publiés et cache les brouillons", function () {
    Setting::set('home.hero_title_fr', 'Titre du bandeau');
    Setting::set('home.mission_fr', 'Notre mission est claire.');
    Setting::set('ambassador.message_fr', 'Chers compatriotes, bienvenue.');
    Make::news(['title_fr' => 'Article publié']);
    Make::news(['title_fr' => 'Article brouillon', 'status' => News::STATUS_DRAFT]);
    Make::service(['title_fr' => 'Service visible']);
    Make::event(['title_fr' => 'Événement à venir']);
    Make::announcement(['title_fr' => 'Avis urgent', 'priority' => 'urgent']);
    Make::holiday(today()->addDays(10)->toDateString(), ['name_fr' => 'Jour férié test']);
    Make::document(['title_fr' => 'Formulaire utile']);

    $this->get('/fr')
        ->assertOk()
        ->assertSee('Titre du bandeau')
        ->assertSee('Notre mission est claire.')
        ->assertSee('Chers compatriotes, bienvenue.')
        ->assertSee('Article publié')
        ->assertDontSee('Article brouillon')
        ->assertSee('Service visible')
        ->assertSee('Événement à venir')
        ->assertSee('Avis urgent')
        ->assertSee('Jour férié test')
        ->assertSee('Formulaire utile');
});

test("le mot de l'ambassadeur disparaît quand il est masqué", function () {
    Setting::set('ambassador.message_fr', 'Message masqué');
    Setting::set('ambassador.published', '0');

    $this->get('/fr')->assertOk()->assertDontSee('Message masqué');
    $this->get('/fr/ambassadeur')->assertNotFound();

    Setting::set('ambassador.published', '1');
    $this->get('/fr/ambassadeur')->assertOk()->assertSee('Message masqué');
});

test('un article publié est lisible dans les deux langues, avec repli sur le français', function () {
    $news = Make::news(['title_fr' => 'Seulement en français', 'title_en' => null]);

    $this->get("/fr/actualites/{$news->slug}")->assertOk()->assertSee('Seulement en français');
    $this->get("/en/actualites/{$news->slug}")->assertOk()->assertSee('Seulement en français')->assertSee('<html lang="en"', false);
});

test('un brouillon, une archive ou un article programmé renvoient 404', function (array $attributes) {
    $news = Make::news($attributes);

    $this->get("/fr/actualites/{$news->slug}")->assertNotFound();
    $this->get('/fr/actualites')->assertOk()->assertDontSee($news->title_fr);
})->with([
    'brouillon' => [['status' => News::STATUS_DRAFT, 'title_fr' => 'Brouillon secret']],
    'archive' => [['status' => News::STATUS_ARCHIVED, 'title_fr' => 'Archive ancienne']],
    'programmé' => [['published_on' => today()->addDays(3), 'title_fr' => 'Article du futur']],
]);

test('la liste des actualités se filtre par catégorie', function () {
    $category = NewsCategory::create(['name_fr' => 'Coopération', 'name_en' => 'Cooperation']);
    Make::news(['title_fr' => 'Dans la catégorie', 'news_category_id' => $category->id]);
    Make::news(['title_fr' => 'Hors catégorie']);

    $this->get('/fr/actualites?category=' . $category->id)
        ->assertOk()
        ->assertSee('Dans la catégorie')
        ->assertDontSee('Hors catégorie');
});

test('un communiqué expiré ou brouillon est introuvable', function () {
    $visible = Make::announcement(['title_fr' => 'Avis en cours', 'expires_on' => today()->addDay()]);
    $expired = Make::announcement(['title_fr' => 'Avis expiré', 'expires_on' => today()->subDay()]);
    $draft = Make::announcement(['title_fr' => 'Avis brouillon', 'status' => 'draft']);

    $this->get("/fr/communiques/{$visible->id}")->assertOk()->assertSee('Avis en cours');
    $this->get("/fr/communiques/{$expired->id}")->assertNotFound();
    $this->get("/fr/communiques/{$draft->id}")->assertNotFound();
    $this->get('/fr/communiques')->assertOk()->assertSee('Avis en cours')->assertDontSee('Avis expiré')->assertDontSee('Avis brouillon');
});

test('les pages éditoriales : publiée visible, brouillon ou inconnue en 404', function () {
    Make::page('about-congo', ['title_fr' => 'Le Congo', 'title_en' => 'Congo']);
    Make::page('invest-in-congo', ['title_fr' => 'Investir', 'status' => Page::STATUS_DRAFT]);

    $this->get('/fr/about-congo')->assertOk()->assertSee('Le Congo');
    $this->get('/en/about-congo')->assertOk()->assertSee('Congo');
    $this->get('/fr/invest-in-congo')->assertNotFound();
    $this->get('/fr/page-inexistante')->assertNotFound()->assertSee('Page introuvable');
    $this->get('/en/page-inexistante')->assertNotFound()->assertSee('Page not found');
});

test('services, événements, galerie : brouillons en 404', function () {
    $service = Make::service();
    $hidden = Make::service(['title_fr' => 'Service caché', 'status' => 'draft']);
    $event = Make::event();
    $draftEvent = Make::event(['status' => 'draft']);
    $album = Album::create(['name_fr' => 'Visite présidentielle']);
    Photo::create(['album_id' => $album->id, 'image_path' => 'gallery/a.jpg', 'is_featured' => true]);

    $this->get('/fr/services')->assertOk()->assertSee($service->title_fr)->assertDontSee('Service caché');
    $this->get("/fr/services/{$service->id}")->assertOk()->assertSee('3 semaines')->assertSee('/fr/rendez-vous?service=' . $service->id, false);
    $this->get("/fr/services/{$hidden->id}")->assertNotFound();
    $this->get("/fr/evenements/{$event->id}")->assertOk()->assertSee($event->title_fr);
    $this->get("/fr/evenements/{$draftEvent->id}")->assertNotFound();
    $this->get('/fr/evenements?tab=past')->assertOk();
    $this->get('/fr/galerie')->assertOk()->assertSee('Visite présidentielle');
    $this->get("/fr/galerie/{$album->id}")->assertOk();
});

test('les pages de liste affichent un état vide soigné', function (string $url, string $text) {
    $this->get($url)->assertOk()->assertSee($text, false);
})->with([
    ['/fr/actualites', 'Aucun article pour le moment'],
    ['/fr/communiques', 'Aucun communiqué en cours'],
    ['/fr/services', 'Les fiches des services arrivent'],
    ['/fr/evenements', 'Aucun événement annoncé'],
    ['/fr/galerie', 'La galerie est en préparation'],
    ['/fr/documents', 'Aucun document disponible'],
    ['/en/documents', 'No documents available'],
]);

test('les pages formulaires et contact répondent', function () {
    $this->get('/fr/rendez-vous')->assertOk()->assertSeeLivewire('appointment-form');
    $this->get('/fr/contact')->assertOk()->assertSeeLivewire('contact-form')->assertSee('Urgence consulaire');
});

test('le sélecteur de langue pointe vers la même page dans l’autre langue', function () {
    $news = Make::news();

    $this->get("/fr/actualites/{$news->slug}")
        ->assertOk()
        ->assertSee('href="' . url("/en/actualites/{$news->slug}") . '"', false)
        ->assertSee('hreflang="en"', false)
        ->assertSee('<link rel="alternate" hreflang="x-default" href="' . url("/fr/actualites/{$news->slug}") . '">', false)
        ->assertSee('<link rel="canonical" href="' . url("/fr/actualites/{$news->slug}") . '">', false);

    $this->get('/en/services')->assertOk()->assertSee('href="' . url('/fr/services') . '"', false);
});

test('le titre de page et les balises de partage sont renseignés', function () {
    $news = Make::news(['title_fr' => "L'ambassadeur reçu"]);

    $this->get("/fr/actualites/{$news->slug}")
        ->assertSee("<title>L&#039;ambassadeur reçu — Ambassade de la République du Congo au Kenya</title>", false)
        ->assertSee('property="og:type" content="article"', false)
        ->assertSee('name="twitter:card"', false);
});
