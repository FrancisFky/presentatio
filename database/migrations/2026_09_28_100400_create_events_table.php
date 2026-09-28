<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title_fr')->nullable();
            $table->string('title_en')->nullable();
            $table->longText('description_fr')->nullable();
            $table->longText('description_en')->nullable();
            $table->string('venue_fr')->nullable();
            $table->string('venue_en')->nullable();
            $table->date('starts_on');
            $table->time('starts_at')->nullable();
            $table->string('organizer', 150)->nullable();
            $table->string('speaker', 150)->nullable();
            $table->string('image_path')->nullable();
            $table->string('registration_url')->nullable();
            $table->string('status', 20)->default('draft');
            $table->timestamps();

            $table->index(['status', 'starts_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
