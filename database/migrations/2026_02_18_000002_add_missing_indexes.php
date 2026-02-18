<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dispatch_entries', function (Blueprint $table) {
            $table->index('vehicle_id');
            $table->index('trip_code_id');
            $table->index('driver_id');
        });

        Schema::table('sms_logs', function (Blueprint $table) {
            $table->index('sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('dispatch_entries', function (Blueprint $table) {
            $table->dropIndex(['vehicle_id']);
            $table->dropIndex(['trip_code_id']);
            $table->dropIndex(['driver_id']);
        });

        Schema::table('sms_logs', function (Blueprint $table) {
            $table->dropIndex(['sent_at']);
        });
    }
};
