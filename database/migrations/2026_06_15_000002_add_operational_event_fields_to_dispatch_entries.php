<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dispatch_entries', function (Blueprint $table) {
            $table->timestamp('delayed_at')->nullable()->after('actual_arrival');
            $table->timestamp('cancelled_at')->nullable()->after('delayed_at');
            $table->timestamp('breakdown_at')->nullable()->after('cancelled_at');
            $table->timestamp('driver1_arrived_at')->nullable()->after('breakdown_at');
            $table->timestamp('driver2_arrived_at')->nullable()->after('driver1_arrived_at');
            $table->timestamp('driver1_cutoff_at')->nullable()->after('driver2_arrived_at');
            $table->timestamp('driver2_cutoff_at')->nullable()->after('driver1_cutoff_at');
            $table->foreignId('replacement_driver1_id')->nullable()->after('driver2_cutoff_at')->constrained('drivers')->nullOnDelete();
            $table->foreignId('replacement_driver2_id')->nullable()->after('replacement_driver1_id')->constrained('drivers')->nullOnDelete();
            $table->string('delay_reason')->nullable()->after('replacement_driver2_id');
            $table->string('cancel_reason')->nullable()->after('delay_reason');
            $table->string('breakdown_reason')->nullable()->after('cancel_reason');
            $table->text('operations_notes')->nullable()->after('breakdown_reason');
        });
    }

    public function down(): void
    {
        Schema::table('dispatch_entries', function (Blueprint $table) {
            $table->dropForeign(['replacement_driver1_id']);
            $table->dropForeign(['replacement_driver2_id']);
            $table->dropColumn([
                'delayed_at',
                'cancelled_at',
                'breakdown_at',
                'driver1_arrived_at',
                'driver2_arrived_at',
                'driver1_cutoff_at',
                'driver2_cutoff_at',
                'replacement_driver1_id',
                'replacement_driver2_id',
                'delay_reason',
                'cancel_reason',
                'breakdown_reason',
                'operations_notes',
            ]);
        });
    }
};
