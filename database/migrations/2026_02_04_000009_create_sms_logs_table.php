<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispatch_entry_id')->nullable()->constrained()->nullOnDelete();
            $table->string('recipient_phone', 20);
            $table->text('message');
            $table->string('status')->default('pending');
            $table->string('provider_message_id', 100)->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
