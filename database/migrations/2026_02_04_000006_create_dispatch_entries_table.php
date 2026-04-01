<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispatch_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispatch_day_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained();
            $table->foreignId('trip_code_id')->nullable()->constrained();
            $table->foreignId('driver_id')->nullable()->constrained('drivers');
            $table->string('brand', 100)->nullable();
            $table->string('bus_number', 20)->nullable();
            $table->string('route', 200)->nullable();
            $table->string('bus_type')->nullable();
            $table->string('departure_terminal', 100)->nullable();
            $table->string('arrival_terminal', 100)->nullable();
            $table->time('scheduled_departure')->nullable();
            $table->time('actual_departure')->nullable();
            $table->string('direction', 2)->nullable();
            $table->string('status')->default('scheduled');
            $table->text('remarks')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['dispatch_day_id', 'sort_order']);
            $table->index('status');
            $table->index('direction');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatch_entries');
    }
};
