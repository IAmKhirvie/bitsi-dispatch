<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->unsignedSmallInteger('speed_kph')->nullable();
            $table->unsignedSmallInteger('heading')->nullable();
            $table->unsignedInteger('kmr')->nullable();
            $table->timestamp('recorded_at')->index();
            $table->string('source', 32)->default('device');
            $table->timestamps();
            $table->index(['vehicle_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_positions');
    }
};
