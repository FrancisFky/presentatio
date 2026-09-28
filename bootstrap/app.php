<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Back-office sous /admin : connexion, OTP, puis gestion du contenu
            Route::middleware('web')->prefix('admin')->group(function () {
                require base_path('routes/auth.php');
                require base_path('routes/admin.php');
            });
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(fn () => route('dashboard'));

        $middleware->alias([
            'auth.admin' => \App\Http\Middleware\AuthenticateAdmin::class,
            'guest.admin' => \App\Http\Middleware\RedirectIfAdminAuthenticated::class,
            'check.otp' => \App\Http\Middleware\CheckOtpVerification::class,
            'can.manage.admins' => \App\Http\Middleware\EnsureAdminCanManageAdmins::class,
            'admin.sensitive' => \App\Http\Middleware\EnsureAdminCanManageSensitive::class,
            'admin.audit' => \App\Http\Middleware\AuditAdminActions::class,
            'locale' => \App\Http\Middleware\SetLocale::class,
        ]);

        // La langue avant la liaison des modèles : un 404 (article introuvable)
        // s'affiche alors dans la langue de l'URL
        $middleware->prependToPriorityList(
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\SetLocale::class,
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
