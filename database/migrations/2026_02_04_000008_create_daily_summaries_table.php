<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispatch_day_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('total_trips')->default(0);
            $table->unsignedInteger('sb_trips')->default(0);
            $table->unsignedInteger('nb_trips')->default(0);
            $table->unsignedInteger('naga_trips')->default(0);
            $table->unsignedInteger('legazpi_trips')->default(0);
            $table->unsignedInteger('sorsogon_trips')->default(0);
            $table->unsignedInteger('virac_trips')->default(0);
            $table->unsignedInteger('masbate_trips')->default(0);
            $table->unsignedInteger('tabaco_trips')->default(0);
            $table->unsignedInteger('visayas_trips')->default(0);
            $table->unsignedInteger('cargo_trips')->default(0);
            $table->timestamps();

            $table->unique('dispatch_day_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_summaries');
    }
};
