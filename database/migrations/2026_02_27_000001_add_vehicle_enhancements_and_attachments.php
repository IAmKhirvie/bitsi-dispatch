<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('current_location')->nullable()->after('status');
            $table->integer('pms_interval_months')->nullable()->after('last_pms_date');
            $table->date('next_pms_date')->nullable()->after('pms_interval_months');
            $table->unsignedBigInteger('total_kilometers')->default(0)->after('current_pms_value');
        });

        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->morphs('attachable');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type')->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('label')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['current_location', 'pms_interval_months', 'next_pms_date', 'total_kilometers']);
        });
    }
};
