<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->unsignedSmallInteger('seating_capacity')->nullable()->after('bus_type');
            $table->unsignedInteger('current_kmr')->default(0)->after('current_pms_value');
            $table->unsignedInteger('last_pms_kmr')->default(0)->after('current_kmr');
        });

        Schema::table('trip_codes', function (Blueprint $table) {
            $table->foreignId('default_vehicle_id')->nullable()->after('id')->constrained('vehicles')->nullOnDelete();
            $table->string('default_brand', 100)->nullable()->after('operator');
            $table->unsignedSmallInteger('default_seating_capacity')->nullable()->after('default_brand');
        });

        Schema::table('dispatch_entries', function (Blueprint $table) {
            $table->unsignedSmallInteger('seating_capacity')->nullable()->after('bus_number');
            $table->unsignedInteger('kmr_at_dispatch')->nullable()->after('actual_departure');
            $table->timestamp('actual_arrival')->nullable()->after('kmr_at_dispatch');
            $table->unsignedInteger('kmr_at_arrival')->nullable()->after('actual_arrival');
            $table->foreignId('dispatcher_user_id')->nullable()->after('driver2_id')->constrained('users')->nullOnDelete();
            $table->index('dispatcher_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('dispatch_entries', function (Blueprint $table) {
            $table->dropForeign(['dispatcher_user_id']);
            $table->dropColumn(['seating_capacity', 'kmr_at_dispatch', 'actual_arrival', 'kmr_at_arrival', 'dispatcher_user_id']);
        });

        Schema::table('trip_codes', function (Blueprint $table) {
            $table->dropForeign(['default_vehicle_id']);
            $table->dropColumn(['default_vehicle_id', 'default_brand', 'default_seating_capacity']);
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['seating_capacity', 'current_kmr', 'last_pms_kmr']);
        });
    }
};
