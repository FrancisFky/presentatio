<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Communiqués et avis officiels : une date de fin les retire du site d'eux-mêmes.
     */
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title_fr')->nullable();
            $table->string('title_en')->nullable();
            $table->longText('body_fr')->nullable();
            $table->longText('body_en')->nullable();
            $table->string('category', 100)->nullable();
            $table->string('priority', 20)->default('normal');
            $table->string('image_path')->nullable();
            $table->string('attachment_path')->nullable();
            $table->date('published_on');
            $table->date('expires_on')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->string('status', 20)->default('draft');
            $table->timestamps();

            $table->index(['status', 'published_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
