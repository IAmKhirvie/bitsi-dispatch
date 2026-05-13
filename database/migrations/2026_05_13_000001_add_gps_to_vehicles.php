<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicles', 'gps_device_id')) {
                $table->string('gps_device_id', 100)->nullable()->unique()->after('bus_number');
            }
            if (!Schema::hasColumn('vehicles', 'last_lat')) {
                $table->decimal('last_lat', 10, 7)->nullable()->after('last_used_at');
            }
            if (!Schema::hasColumn('vehicles', 'last_lng')) {
                $table->decimal('last_lng', 10, 7)->nullable()->after('last_lat');
            }
            if (!Schema::hasColumn('vehicles', 'last_position_at')) {
                $table->timestamp('last_position_at')->nullable()->after('last_lng');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            if (Schema::hasColumn('vehicles', 'gps_device_id')) {
                $table->dropUnique(['gps_device_id']);
                $table->dropColumn('gps_device_id');
            }
            $table->dropColumn(['last_lat', 'last_lng', 'last_position_at']);
        });
    }
};
