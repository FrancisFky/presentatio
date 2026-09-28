<?php

use App\Http\Controllers\AuthController;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

pest()->extend(Tests\TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/** Un administrateur actif, déjà passé par l'étape du code e-mail */
function actingAsAdmin(string $role = Admin::ROLE_ADMIN): Admin
{
    $admin = createAdmin(['role' => $role]);

    test()->actingAs($admin, 'admin')->withSession([AuthController::OTP_SESSION_KEY => $admin->id]);

    return $admin;
}

function createAdmin(array $attributes = []): Admin
{
    return Admin::create($attributes + [
        'slug' => (string) Str::orderedUuid(),
        'name' => 'Agent ' . Str::random(4),
        'username' => 'agent_' . Str::lower(Str::random(8)),
        'email' => Str::lower(Str::random(8)) . '@ambassade.test',
        'password' => 'motdepasse-solide',
        'role' => Admin::ROLE_ADMIN,
        'status' => Admin::STATUS_ACTIVE,
    ]);
}
