<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->nullable()->unique();
            $table->string('name');
            $table->string('username', 50)->unique();
            $table->string('email', 100)->unique();
            $table->string('phone', 20)->nullable();
            $table->string('password');

            // Code à 6 chiffres envoyé par e-mail à chaque connexion
            $table->string('otp_code', 10)->nullable();
            $table->timestamp('otp_expires_at')->nullable();

            // Lien d'activation : l'administrateur choisit lui-même son mot de passe
            $table->string('activation_token', 64)->nullable();
            $table->timestamp('activation_sent_at')->nullable();

            $table->enum('role', ['super_admin', 'admin', 'member'])->default('member');

            // 0 : jamais activé, 1 : actif, 2 : désactivé
            $table->unsignedTinyInteger('status')->default(0);
            $table->timestamp('last_activity_at')->nullable();
            $table->rememberToken();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
