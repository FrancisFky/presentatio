<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name_fr', 100);
            $table->string('name_en', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->foreignId('news_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title_fr')->nullable();
            $table->string('title_en')->nullable();
            $table->text('excerpt_fr')->nullable();
            $table->text('excerpt_en')->nullable();
            $table->longText('body_fr')->nullable();
            $table->longText('body_en')->nullable();
            $table->string('image_path')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('author', 150)->nullable();
            $table->date('published_on');
            $table->string('status', 20)->default('draft');
            $table->timestamps();

            $table->index(['status', 'published_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news');
        Schema::dropIfExists('news_categories');
    }
};
