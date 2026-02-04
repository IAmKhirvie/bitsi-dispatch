<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dispatch_entries', function (Blueprint $table) {
            $table->foreignId('driver2_id')->nullable()->after('driver_id')->constrained('drivers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('dispatch_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('driver2_id');
        });
    }
};
