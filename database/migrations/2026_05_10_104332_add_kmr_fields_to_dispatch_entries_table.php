<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dispatch_entries', function (Blueprint $table) {
            $table->unsignedInteger('start_kmr')->nullable()->after('arrived_at');
            $table->unsignedInteger('end_kmr')->nullable()->after('start_kmr');
        });
    }

    public function down(): void
    {
        Schema::table('dispatch_entries', function (Blueprint $table) {
            $table->dropColumn(['start_kmr', 'end_kmr']);
        });
    }
};