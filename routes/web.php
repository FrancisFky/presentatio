<?php

use App\Http\Controllers\Site;
use App\Support\Locales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Sans langue dans l'URL : celle du navigateur si on la parle, sinon le français
Route::get('/', function (Request $request) {
    return redirect()->route('site.home', ['locale' => $request->getPreferredLanguage(Locales::SUPPORTED) ?? Locales::DEFAULT]);
});

Route::get('sitemap.xml', [Site\SitemapController::class, 'index'])->name('sitemap');

// Généré plutôt que statique : l'adresse du plan du site suit APP_URL
Route::get('robots.txt', [Site\SitemapController::class, 'robots'])->name('robots');

// Anciennes adresses du site PHP : redirections permanentes pour les liens et Google
Route::permanentRedirect('index.php', '/fr');
Route::permanentRedirect('appointment.php', '/fr/rendez-vous');
Route::permanentRedirect('about-congo.php', '/fr/about-congo');
Route::permanentRedirect('about-embassy.php', '/fr/about-embassy');
Route::permanentRedirect('invest-in-congo.php', '/fr/invest-in-congo');

Route::prefix('{locale}')
    ->whereIn('locale', Locales::SUPPORTED)
    ->middleware('locale')
    ->name('site.')
    ->group(function () {
        Route::get('/', [Site\HomeController::class, 'index'])->name('home');

        Route::get('actualites', [Site\NewsController::class, 'index'])->name('news.index');
        Route::get('actualites/{news}', [Site\NewsController::class, 'show'])->name('news.show');

        Route::get('communiques', [Site\AnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('communiques/{announcement}', [Site\AnnouncementController::class, 'show'])->name('announcements.show');

        Route::get('services', [Site\ServiceController::class, 'index'])->name('services.index');
        Route::get('services/{service}', [Site\ServiceController::class, 'show'])->name('services.show');

        Route::get('evenements', [Site\EventController::class, 'index'])->name('events.index');
        Route::get('evenements/{event}', [Site\EventController::class, 'show'])->name('events.show');

        Route::get('galerie', [Site\GalleryController::class, 'index'])->name('gallery.index');
        Route::get('galerie/{album}', [Site\GalleryController::class, 'show'])->name('gallery.show');

        Route::get('documents', [Site\DocumentController::class, 'index'])->name('documents.index');
        Route::get('documents/{document}/telecharger', [Site\DocumentController::class, 'download'])->name('documents.download');

        Route::get('rendez-vous', [Site\ContactController::class, 'appointment'])->name('appointment');
        Route::get('contact', [Site\ContactController::class, 'contact'])->name('contact');
        Route::get('ambassadeur', [Site\PageController::class, 'ambassador'])->name('ambassador');

        // En dernier : /fr/about-congo, /fr/about-embassy, /fr/invest-in-congo
        Route::get('{page}', [Site\PageController::class, 'show'])->name('page');
    });
