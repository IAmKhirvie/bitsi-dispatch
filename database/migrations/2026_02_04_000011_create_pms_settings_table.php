<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pms_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('unit')->default('kilometers');
            $table->unsignedInteger('threshold');
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pms_settings');
    }
};
