<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispatch_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispatch_entry_id')->constrained()->cascadeOnDelete();
            $table->string('event_type', 50)->index();
            $table->timestamp('occurred_at')->index();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->string('old_value')->nullable();
            $table->string('new_value')->nullable();
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['dispatch_entry_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatch_events');
    }
};
