<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dispatch_entries', function (Blueprint $table) {
            $table->index('driver2_id');
        });

        Schema::table('sms_logs', function (Blueprint $table) {
            $table->index('dispatch_entry_id');
        });

        Schema::table('driver_attendances', function (Blueprint $table) {
            $table->index('dispatch_entry_id');
        });

        Schema::table('attachments', function (Blueprint $table) {
            $table->index('uploaded_by');
        });
    }

    public function down(): void
    {
        Schema::table('dispatch_entries', function (Blueprint $table) {
            $table->dropIndex(['driver2_id']);
        });

        Schema::table('sms_logs', function (Blueprint $table) {
            $table->dropIndex(['dispatch_entry_id']);
        });

        Schema::table('driver_attendances', function (Blueprint $table) {
            $table->dropIndex(['dispatch_entry_id']);
        });

        Schema::table('attachments', function (Blueprint $table) {
            $table->dropIndex(['uploaded_by']);
        });
    }
};
