<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Premier compte, à partir de SUPER_ADMIN_* dans .env. Sans mot de passe
 * fourni, un mot de passe aléatoire est affiché une seule fois : pas de
 * compte « admin / password » comme sur l'ancien site.
 */
class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('SUPER_ADMIN_EMAIL', 'admin@ambassade-congo.ke');

        if (Admin::where('email', $email)->exists()) {
            return;
        }

        $password = env('SUPER_ADMIN_PASSWORD') ?: Str::password(16, symbols: false);

        Admin::create([
            'slug' => (string) Str::orderedUuid(),
            'name' => env('SUPER_ADMIN_NAME', 'Administrateur'),
            'username' => 'super_admin',
            'email' => $email,
            'password' => $password,
            'role' => Admin::ROLE_SUPER_ADMIN,
            'status' => Admin::STATUS_ACTIVE,
        ]);

        if (!env('SUPER_ADMIN_PASSWORD')) {
            $this->command?->warn("Super administrateur créé : {$email} / {$password}");
            $this->command?->warn('Notez ce mot de passe maintenant : il ne sera plus affiché.');
        }
    }
}
