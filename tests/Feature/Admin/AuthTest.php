<?php

use App\Http\Controllers\AuthController;
use App\Mail\OtpSent;
use App\Models\Admin;
use Illuminate\Support\Facades\Mail;

beforeEach(fn () => Mail::fake());

it('affiche la page de connexion', function () {
    $this->get('/admin/login')->assertOk()->assertSee('Espace administration');
});

it("renvoie vers la connexion sans être connecté", function () {
    $this->get('/admin/dashboard')->assertRedirect('/admin/login');
    $this->get('/admin/news')->assertRedirect('/admin/login');
});

it('envoie un code par e-mail après le mot de passe, puis ouvre le tableau de bord', function () {
    $admin = createAdmin(['email' => 'agent@ambassade.test']);

    $this->post('/admin/login', ['email' => 'agent@ambassade.test', 'password' => 'motdepasse-solide'])
        ->assertRedirect(route('otp.verify'));

    Mail::assertSent(OtpSent::class, fn ($mail) => $mail->hasTo('agent@ambassade.test'));

    // Sans le code, l'admin reste fermé
    $this->get('/admin/dashboard')->assertRedirect(route('otp.verify', ['email' => $admin->email]));

    $this->post('/admin/verify-otp', ['otp' => '000000'])->assertSessionHasErrors('otp');

    $this->post('/admin/verify-otp', ['otp' => $admin->fresh()->otp_code])->assertRedirect(route('dashboard'));
    $this->get('/admin/dashboard')->assertOk();
});

it('refuse un mauvais mot de passe et bloque le compte après cinq essais', function () {
    createAdmin(['email' => 'agent@ambassade.test']);

    foreach (range(1, 5) as $i) {
        $this->post('/admin/login', ['email' => 'agent@ambassade.test', 'password' => 'mauvais-mot-de-passe'])->assertSessionHasErrors('email');
    }

    // Même le bon mot de passe est refusé pendant le blocage
    $this->post('/admin/login', ['email' => 'agent@ambassade.test', 'password' => 'motdepasse-solide'])
        ->assertSessionHasErrors(['email' => "Trop d'essais de mot de passe pour ce compte. Réessayez dans 15 minute(s) ou réinitialisez votre mot de passe."]);
    Mail::assertNothingSent();
});

it("refuse un compte désactivé ou jamais activé", function (int $status) {
    createAdmin(['email' => 'agent@ambassade.test', 'status' => $status]);

    $this->post('/admin/login', ['email' => 'agent@ambassade.test', 'password' => 'motdepasse-solide'])->assertSessionHasErrors('email');
    $this->assertGuest('admin');
})->with([Admin::STATUS_INACTIVE, Admin::STATUS_DEACTIVATED]);

it('déconnecte', function () {
    actingAsAdmin();

    $this->post('/admin/logout')->assertRedirect(route('login'));
    $this->assertGuest('admin');
});

it("n'est pas indexé par les moteurs de recherche", function () {
    $this->get('/admin/login')->assertSee('noindex, nofollow', false);
});

it('accepte le code de test fixe en local seulement', function (string $env, bool $fixed) {
    config(['auth.test_otp' => '111111']);
    app()['env'] = $env;
    $admin = createAdmin();

    $admin->generateAndSendEmailOtp();

    expect($admin->fresh()->otp_code === '111111')->toBe($fixed);
})->with([['local', true], ['production', false], ['testing', false]]);
