<?php

use App\Models\Admin;
use App\Models\AdminAuditLog;
use App\Models\News;
use Illuminate\Support\Facades\Route;

it('laisse un éditeur écrire mais pas supprimer', function () {
    actingAsAdmin(Admin::ROLE_MEMBER);
    $news = News::create(['slug' => 'a', 'title_fr' => 'Article', 'published_on' => today(), 'status' => 'draft']);

    $this->get(route('news.edit', $news))->assertOk();
    $this->delete(route('news.destroy', $news))->assertForbidden();
    expect(News::count())->toBe(1);
});

it('protège toutes les suppressions de l\'admin', function () {
    $unprotected = collect(Route::getRoutes()->getRoutes())
        ->filter(fn ($route) => in_array('DELETE', $route->methods()) && str_starts_with($route->uri(), 'admin/'))
        ->reject(fn ($route) => array_intersect(['admin.sensitive', 'can.manage.admins'], $route->gatherMiddleware()))
        ->map->getName()
        ->values()
        ->all();

    expect($unprotected)->toBe(['photos.destroy']);
});

it('laisse un administrateur supprimer', function () {
    actingAsAdmin(Admin::ROLE_ADMIN);
    $news = News::create(['slug' => 'a', 'title_fr' => 'Article', 'published_on' => today(), 'status' => 'draft']);

    $this->delete(route('news.destroy', $news))->assertRedirect(route('news.index'));
    expect(News::count())->toBe(0);
});

it("réserve les réglages du site et la gestion des comptes aux administrateurs", function () {
    actingAsAdmin(Admin::ROLE_MEMBER);

    $this->get(route('settings.edit', 'site'))->assertForbidden();
    $this->put(route('settings.update', 'site'), ['email' => 'pirate@example.com'])->assertForbidden();
    $this->get(route('admins.index'))->assertForbidden();

    // L'accueil et l'ambassadeur restent modifiables par les éditeurs
    $this->get(route('settings.edit', 'home'))->assertOk();
});

it("n'affiche pas les boutons de suppression aux éditeurs", function () {
    actingAsAdmin(Admin::ROLE_MEMBER);
    News::create(['slug' => 'a', 'title_fr' => 'Article', 'published_on' => today(), 'status' => 'draft']);

    $this->get(route('news.index'))->assertOk()->assertDontSee('title="Supprimer"', false);
});

it('journalise les modifications, pas les consultations', function () {
    $admin = actingAsAdmin();

    $this->get(route('news.index'));
    expect(AdminAuditLog::count())->toBe(0);

    $this->post(route('news.store'), ['title_fr' => 'Nouvelle', 'published_on' => today()->toDateString(), 'status' => 'draft']);
    expect(AdminAuditLog::sole())->admin_id->toBe($admin->id)->action->toBe('news.store');
});

it('rend chaque écran de l\'admin', function (string $route, array $params = []) {
    actingAsAdmin(Admin::ROLE_SUPER_ADMIN);

    $this->get(route($route, $params))->assertOk();
})->with([
    ['dashboard'], ['news.index'], ['news.create'], ['announcements.index'], ['announcements.create'],
    ['events.index'], ['events.create'], ['services.index'], ['services.create'], ['documents.index'], ['documents.create'],
    ['holidays.index'], ['holidays.create'], ['pages.index'], ['albums.index'], ['appointments.index'], ['messages.index'],
    ['settings.edit', ['group' => 'home']], ['settings.edit', ['group' => 'ambassador']], ['settings.edit', ['group' => 'contacts']], ['settings.edit', ['group' => 'site']],
    ['admins.index'], ['admins.create'], ['admins.audit'], ['profile'],
]);

it('renvoie 404 pour un groupe de réglages inconnu', function () {
    actingAsAdmin();
    $this->get('/admin/settings/inconnu')->assertNotFound();
});
