<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Helper function to check if a column exists in a table (SQLite)
        $columnExists = function ($table, $column) {
            $result = DB::select("PRAGMA table_info($table)");
            return collect($result)->contains('name', $column);
        };

        // ---------- Vehicles ----------
        if (!$columnExists('vehicles', 'seating_capacity')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->unsignedSmallInteger('seating_capacity')->nullable()->after('bus_type');
            });
        }
        if (!$columnExists('vehicles', 'current_kmr')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->unsignedInteger('current_kmr')->default(0)->after('current_pms_value');
            });
        }
        if (!$columnExists('vehicles', 'last_pms_kmr')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->unsignedInteger('last_pms_kmr')->default(0)->after('current_kmr');
            });
        }

        // ---------- Trip Codes ----------
        if (!$columnExists('trip_codes', 'default_vehicle_id')) {
            Schema::table('trip_codes', function (Blueprint $table) {
                $table->foreignId('default_vehicle_id')->nullable()->after('id')->constrained('vehicles')->nullOnDelete();
            });
        }
        if (!$columnExists('trip_codes', 'default_brand')) {
            Schema::table('trip_codes', function (Blueprint $table) {
                $table->string('default_brand', 100)->nullable()->after('operator');
            });
        }
        if (!$columnExists('trip_codes', 'default_seating_capacity')) {
            Schema::table('trip_codes', function (Blueprint $table) {
                $table->unsignedSmallInteger('default_seating_capacity')->nullable()->after('default_brand');
            });
        }

        // ---------- Dispatch Entries ----------
        if (!$columnExists('dispatch_entries', 'seating_capacity')) {
            Schema::table('dispatch_entries', function (Blueprint $table) {
                $table->unsignedSmallInteger('seating_capacity')->nullable()->after('bus_number');
            });
        }
        if (!$columnExists('dispatch_entries', 'kmr_at_dispatch')) {
            Schema::table('dispatch_entries', function (Blueprint $table) {
                $table->unsignedInteger('kmr_at_dispatch')->nullable()->after('actual_departure');
            });
        }
        if (!$columnExists('dispatch_entries', 'actual_arrival')) {
            Schema::table('dispatch_entries', function (Blueprint $table) {
                $table->timestamp('actual_arrival')->nullable()->after('kmr_at_dispatch');
            });
        }
        if (!$columnExists('dispatch_entries', 'kmr_at_arrival')) {
            Schema::table('dispatch_entries', function (Blueprint $table) {
                $table->unsignedInteger('kmr_at_arrival')->nullable()->after('actual_arrival');
            });
        }
        if (!$columnExists('dispatch_entries', 'dispatcher_user_id')) {
            Schema::table('dispatch_entries', function (Blueprint $table) {
                $table->foreignId('dispatcher_user_id')->nullable()->after('driver2_id')->constrained('users')->nullOnDelete();
                $table->index('dispatcher_user_id');
            });
        }
    }

    public function down(): void
    {
        // Helper same as above
        $columnExists = function ($table, $column) {
            $result = DB::select("PRAGMA table_info($table)");
            return collect($result)->contains('name', $column);
        };

        // Drop columns only if they exist
        Schema::table('dispatch_entries', function (Blueprint $table) use ($columnExists) {
            $table->dropForeign(['dispatcher_user_id']);
            $cols = ['seating_capacity', 'kmr_at_dispatch', 'actual_arrival', 'kmr_at_arrival', 'dispatcher_user_id'];
            foreach ($cols as $col) {
                if ($columnExists('dispatch_entries', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('trip_codes', function (Blueprint $table) use ($columnExists) {
            $table->dropForeign(['default_vehicle_id']);
            $cols = ['default_vehicle_id', 'default_brand', 'default_seating_capacity'];
            foreach ($cols as $col) {
                if ($columnExists('trip_codes', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('vehicles', function (Blueprint $table) use ($columnExists) {
            $cols = ['seating_capacity', 'current_kmr', 'last_pms_kmr'];
            foreach ($cols as $col) {
                if ($columnExists('vehicles', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};