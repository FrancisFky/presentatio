<?php

use App\Mail\AppointmentStatusChanged;
use App\Models\Album;
use App\Models\Appointment;
use App\Models\Document;
use App\Models\Message;
use App\Models\News;
use App\Models\Page;
use App\Models\Photo;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    actingAsAdmin();
});

it("crée un article : adresse tirée du titre, photo en WebP, HTML nettoyé", function () {
    $this->post(route('news.store'), [
        'title_fr' => 'Visite du Président à Nairobi',
        'title_en' => 'President visits Nairobi',
        'body_fr' => '<p>Texte <strong>gras</strong><script>alert(1)</script><a href="javascript:alert(1)">lien</a></p>',
        'published_on' => '2026-09-01',
        'status' => 'published',
        'image' => UploadedFile::fake()->image('photo.jpg', 2400, 1600),
    ])->assertRedirect(route('news.index'));

    $news = News::sole();
    expect($news->slug)->toBe('visite-du-president-a-nairobi')
        ->and($news->body_fr)->not->toContain('script')->not->toContain('javascript:')->toContain('<strong>gras</strong>')
        ->and($news->image_path)->toEndWith('.webp');
    Storage::disk('public')->assertExists($news->image_path);

    // Même titre : une adresse différente
    $this->post(route('news.store'), ['title_fr' => 'Visite du Président à Nairobi', 'published_on' => '2026-09-02', 'status' => 'draft']);
    expect(News::latest('id')->first()->slug)->toBe('visite-du-president-a-nairobi-2');
});

it('exige le titre en français', function () {
    $this->post(route('news.store'), ['title_en' => 'Only English', 'published_on' => '2026-09-01', 'status' => 'draft'])
        ->assertSessionHasErrors('title_fr');
});

it("remplace et retire la photo d'un article", function () {
    $this->post(route('news.store'), ['title_fr' => 'A', 'published_on' => '2026-09-01', 'status' => 'draft', 'image' => UploadedFile::fake()->image('a.jpg')]);
    $news = News::sole();
    $first = $news->image_path;

    $this->put(route('news.update', $news), ['title_fr' => 'A', 'published_on' => '2026-09-01', 'status' => 'draft', 'image' => UploadedFile::fake()->image('b.png')]);
    Storage::disk('public')->assertMissing($first);

    $this->put(route('news.update', $news), ['title_fr' => 'A', 'published_on' => '2026-09-01', 'status' => 'draft', 'remove_image' => '1']);
    expect($news->fresh()->image_path)->toBeNull();
});

it('refuse un fichier déguisé en image', function () {
    $this->post(route('news.store'), [
        'title_fr' => 'A', 'published_on' => '2026-09-01', 'status' => 'draft',
        'image' => UploadedFile::fake()->create('shell.php.jpg', 10, 'application/x-php'),
    ])->assertSessionHasErrors('image');
});

it('ajoute un document avec sa taille et son nom', function () {
    $this->post(route('documents.store'), [
        'title_fr' => 'Formulaire de visa', 'category' => 'Visas', 'status' => 'published',
        'file' => UploadedFile::fake()->create('Formulaire Visa.pdf', 300, 'application/pdf'),
    ])->assertRedirect(route('documents.index'));

    $document = Document::sole();
    expect($document->file_name)->toBe('Formulaire Visa.pdf')->and($document->file_size)->toBe(300 * 1024);
    Storage::disk('public')->assertExists($document->file_path);

    $this->post(route('documents.store'), ['title_fr' => 'Sans fichier', 'status' => 'published'])->assertSessionHasErrors('file');
    $this->post(route('documents.store'), ['title_fr' => 'Script', 'status' => 'published', 'file' => UploadedFile::fake()->create('x.php', 1)])->assertSessionHasErrors('file');
});

it('envoie plusieurs photos dans un album et en met une en avant', function () {
    $album = Album::create(['name_fr' => 'Réceptions']);

    $this->post(route('photos.store', $album), ['photos' => [UploadedFile::fake()->image('1.jpg'), UploadedFile::fake()->image('2.png')]])
        ->assertSessionHas('success', '2 photos ajoutées.');

    $photo = Photo::first();
    $this->put(route('photos.feature', $photo));
    expect($photo->fresh()->is_featured)->toBeTrue();

    $this->delete(route('photos.destroy', $photo));
    Storage::disk('public')->assertMissing($photo->image_path);
});

it("enregistre les réglages d'un groupe", function () {
    $this->put(route('settings.update', 'home'), [
        'hero_title_fr' => 'Bienvenue', 'hero_title_en' => 'Welcome',
        'hero_image' => UploadedFile::fake()->image('hero.jpg', 3000, 1500),
    ])->assertSessionHas('success');

    expect(Setting::get('home.hero_title_fr'))->toBe('Bienvenue')
        ->and(Setting::localized('home.hero_title', 'en'))->toBe('Welcome')
        ->and(Setting::get('home.hero_image'))->toEndWith('.webp');

    $this->put(route('settings.update', 'ambassador'), ['published' => '0', 'biography_fr' => '<p>Bio</p><img src=x onerror=alert(1)>']);
    expect(Setting::get('ambassador.published'))->toBe('0')->and(Setting::get('ambassador.biography_fr'))->toBe('<p>Bio</p>');
});

it("refuse une URL qui n'est pas http(s) dans les réglages", function () {
    $this->put(route('settings.update', 'site'), ['facebook' => 'javascript:alert(1)'])->assertSessionHasErrors('facebook');
});

it('met à jour une page fixe', function () {
    $page = Page::create(['slug' => 'about-congo', 'title_fr' => 'À propos', 'status' => 'draft']);

    $this->put(route('pages.update', $page), ['title_fr' => 'À propos du Congo', 'body_fr' => '<h2>Histoire</h2>', 'status' => 'published'])
        ->assertRedirect(route('pages.index'));

    expect($page->fresh())->status->toBe('published')->body_fr->toBe('<h2>Histoire</h2>');
});

it("confirme un rendez-vous et prévient le demandeur dans sa langue", function () {
    Mail::fake();
    $appointment = Appointment::create([
        'name' => 'Jane', 'email' => 'jane@example.com', 'service_label' => 'Visa',
        'preferred_date' => today()->addWeek(), 'preferred_time' => '10:00', 'locale' => 'en',
    ]);
    expect($appointment->reference)->toStartWith('RDV-');

    $this->put(route('appointments.update', $appointment), [
        'status' => 'confirmed', 'preferred_date' => today()->addWeek()->toDateString(), 'preferred_time' => '11:30',
        'notify' => '1', 'message' => 'Bring your passport.',
    ])->assertSessionHas('success');

    expect($appointment->fresh())->status->toBe('confirmed')->preferred_time->toBe('11:30');
    Mail::assertSent(AppointmentStatusChanged::class, fn ($mail) => $mail->hasTo('jane@example.com') && $mail->locale === 'en');
});

it('marque un message comme lu en l\'ouvrant', function () {
    $message = Message::create(['name' => 'Paul', 'email' => 'paul@example.com', 'body' => 'Bonjour']);

    $this->get(route('messages.show', $message))->assertOk()->assertSee('Bonjour');
    expect($message->fresh())->status->toBe('read')->read_at->not->toBeNull();

    $this->put(route('messages.archive', $message));
    expect($message->fresh()->status)->toBe('archived');
});

it("garde le nom du service sur les rendez-vous quand on le supprime", function () {
    $service = Service::create(['title_fr' => 'Passeport', 'status' => 'published']);
    $appointment = Appointment::create(['name' => 'A', 'email' => 'a@example.com', 'service_id' => $service->id, 'preferred_date' => today(), 'preferred_time' => '09:00']);

    $this->delete(route('services.destroy', $service));

    expect($appointment->fresh())->service_id->toBeNull()->serviceName()->toBe('Passeport');
});
