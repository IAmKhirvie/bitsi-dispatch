<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the unique index if it exists
        if (Schema::hasIndex('vehicles', 'vehicles_gps_device_id_unique')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->dropUnique(['gps_device_id']);
            });
        }

        // Now drop the column
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('gps_device_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('gps_device_id')->nullable()->after('status')->unique();
        });
    }
};
