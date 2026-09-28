<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Installation : le premier compte, les pages fixes, les rubriques et albums
     * de base. Le contenu de démonstration est à part (DemoSeeder).
     */
    public function run(): void
    {
        $this->call([
            SuperAdminSeeder::class,
            BaseContentSeeder::class,
        ]);
    }
}
