<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('driver_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dispatch_entry_id')->nullable()->constrained('dispatch_entries')->cascadeOnDelete();
            $table->date('attendance_date');
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->enum('status', ['pending', 'on_time', 'late', 'absent', 'excused'])->default('pending');
            $table->integer('minutes_late')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('marked_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index(['driver_id', 'attendance_date']);
            $table->index('attendance_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_attendances');
    }
};
