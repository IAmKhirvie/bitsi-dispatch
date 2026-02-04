<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('operator', 100);
            $table->string('origin_terminal', 100);
            $table->string('destination_terminal', 100);
            $table->string('bus_type');
            $table->time('scheduled_departure_time');
            $table->string('direction', 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('direction');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_codes');
    }
};
