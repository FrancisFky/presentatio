<?php

use App\Http\Controllers\Admin\AlbumController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\NewsCategoryController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PhotoController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\AdminAuditLogController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

// Activation d'un compte : lien reçu par e-mail, avant toute connexion
Route::get('admins/activate/{token}', [AdminController::class, 'activate'])->name('admin.activate');
Route::post('admins/activate/{token}', [AdminController::class, 'processActivation'])->middleware('throttle:5,1')->name('admin.process-activation');
Route::get('admins/contact', fn () => view('admin-contact'))->name('admin.contact');

Route::middleware(['auth.admin', 'check.otp', 'admin.audit'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Contenu : les éditeurs créent et modifient, seuls les administrateurs suppriment
    Route::resource('news', NewsController::class)->except('show')->parameters(['news' => 'news'])->middlewareFor('destroy', 'admin.sensitive');
    Route::post('news-categories', [NewsCategoryController::class, 'store'])->name('news-categories.store');
    Route::put('news-categories/{newsCategory}', [NewsCategoryController::class, 'update'])->name('news-categories.update');
    Route::delete('news-categories/{newsCategory}', [NewsCategoryController::class, 'destroy'])->name('news-categories.destroy')->middleware('admin.sensitive');

    Route::resource('announcements', AnnouncementController::class)->except('show')->middlewareFor('destroy', 'admin.sensitive');
    Route::resource('events', EventController::class)->except('show')->middlewareFor('destroy', 'admin.sensitive');
    Route::resource('services', ServiceController::class)->except('show')->middlewareFor('destroy', 'admin.sensitive');
    Route::resource('documents', DocumentController::class)->except('show')->middlewareFor('destroy', 'admin.sensitive');
    Route::resource('holidays', HolidayController::class)->except('show')->middlewareFor('destroy', 'admin.sensitive');

    Route::get('pages', [PageController::class, 'index'])->name('pages.index');
    Route::get('pages/{page}/edit', [PageController::class, 'edit'])->name('pages.edit');
    Route::put('pages/{page}', [PageController::class, 'update'])->name('pages.update');

    // Galerie : albums et photos
    Route::get('gallery', [AlbumController::class, 'index'])->name('albums.index');
    Route::post('gallery/albums', [AlbumController::class, 'store'])->name('albums.store');
    Route::get('gallery/albums/{album}', [AlbumController::class, 'show'])->name('albums.show');
    Route::put('gallery/albums/{album}', [AlbumController::class, 'update'])->name('albums.update');
    Route::delete('gallery/albums/{album}', [AlbumController::class, 'destroy'])->name('albums.destroy')->middleware('admin.sensitive');
    Route::post('gallery/albums/{album}/photos', [PhotoController::class, 'store'])->name('photos.store');
    Route::put('gallery/photos/{photo}', [PhotoController::class, 'update'])->name('photos.update');
    Route::put('gallery/photos/{photo}/feature', [PhotoController::class, 'toggleFeatured'])->name('photos.feature');
    // Exception voulue : un éditeur peut retirer une photo qu'il vient d'envoyer par erreur
    Route::delete('gallery/photos/{photo}', [PhotoController::class, 'destroy'])->name('photos.destroy');

    // Demandes reçues du site
    Route::get('appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
    Route::put('appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
    Route::delete('appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy')->middleware('admin.sensitive');

    Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('messages/{message}', [MessageController::class, 'show'])->name('messages.show');
    Route::put('messages/{message}/archive', [MessageController::class, 'archive'])->name('messages.archive');
    Route::delete('messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy')->middleware('admin.sensitive');

    // Réglages (accueil, ambassadeur, contacts d'urgence, site)
    Route::get('settings/{group}', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings/{group}', [SettingController::class, 'update'])->name('settings.update');

    // Comptes administrateurs
    Route::middleware('can.manage.admins')->group(function () {
        Route::get('admins/audit', [AdminAuditLogController::class, 'index'])->name('admins.audit');
        Route::get('admins', [AdminController::class, 'index'])->name('admins.index');
        Route::get('admins/export', [AdminController::class, 'export'])->name('admins.export');
        Route::get('admins/create', [AdminController::class, 'create'])->name('admins.create');
        Route::post('admins', [AdminController::class, 'store'])->name('admins.store');
        Route::get('admins/{admin}', [AdminController::class, 'show'])->name('admins.show');
        Route::post('admins/{admin}/send-activation', [AdminController::class, 'sendActivation'])->name('admins.send-activation');
        Route::put('admins/{admin}/deactivate', [AdminController::class, 'deactivate'])->name('admins.deactivate');
        Route::put('admins/{admin}/reactivate', [AdminController::class, 'reactivate'])->name('admins.reactivate');
        Route::delete('admins/{admin}', [AdminController::class, 'destroy'])->name('admins.destroy');
    });
});
