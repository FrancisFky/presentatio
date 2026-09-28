<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Services consulaires : passeports, visas, légalisations…
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title_fr')->nullable();
            $table->string('title_en')->nullable();
            $table->string('icon', 50)->nullable();
            foreach (['description', 'requirements', 'documents', 'fees'] as $field) {
                $table->longText($field . '_fr')->nullable();
                $table->longText($field . '_en')->nullable();
            }
            foreach (['processing_time', 'office_hours'] as $field) {
                $table->string($field . '_fr')->nullable();
                $table->string($field . '_en')->nullable();
            }
            $table->unsignedInteger('position')->default(0);
            $table->string('status', 20)->default('draft')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
