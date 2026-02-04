<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispatch_days', function (Blueprint $table) {
            $table->id();
            $table->date('service_date')->unique();
            $table->foreignId('created_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('service_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatch_days');
    }
};
