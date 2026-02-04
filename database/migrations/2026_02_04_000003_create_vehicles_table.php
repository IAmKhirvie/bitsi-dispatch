<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('bus_number', 20)->unique();
            $table->string('brand', 100);
            $table->string('bus_type');
            $table->string('plate_number', 20)->unique();
            $table->string('status')->default('OK');
            $table->string('gps_device_id', 100)->nullable()->unique();
            $table->string('pms_unit')->default('kilometers');
            $table->unsignedInteger('pms_threshold')->default(15000);
            $table->unsignedInteger('current_pms_value')->default(0);
            $table->timestamp('last_pms_date')->nullable();
            $table->unsignedInteger('idle_days')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('bus_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
