<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('albums', function (Blueprint $table) {
            $table->id();
            $table->string('name_fr', 150);
            $table->string('name_en', 150)->nullable();
            $table->text('description_fr')->nullable();
            $table->text('description_en')->nullable();
            $table->timestamps();
        });

        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('album_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title_fr', 150)->nullable();
            $table->string('title_en', 150)->nullable();
            $table->text('caption_fr')->nullable();
            $table->text('caption_en')->nullable();
            $table->string('image_path');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photos');
        Schema::dropIfExists('albums');
    }
};
