<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pms_settings', function (Blueprint $table) {
            $table->decimal('warning_ratio', 4, 2)->default(0.80)->after('threshold');
        });
    }

    public function down(): void
    {
        Schema::table('pms_settings', function (Blueprint $table) {
            $table->dropColumn('warning_ratio');
        });
    }
};
