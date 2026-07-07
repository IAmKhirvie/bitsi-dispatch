<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dispatch_entries', function (Blueprint $table) {
            $table->string('dispatcher_name')->nullable()->after('end_kmr');
        });
    }

    public function down(): void
    {
        Schema::table('dispatch_entries', function (Blueprint $table) {
            $table->dropColumn('dispatcher_name');
        });
    }
};