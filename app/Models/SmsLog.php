<?php

namespace App\Models;

use App\Enums\SmsStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsLog extends Model
{
    protected $fillable = [
        "dispatch_entry_id",
        "recipient_phone",
        "message",
        "status",
        "provider_message_id",
        "sent_at",
    ];

    protected $casts = [
        "status" => SmsStatus::class,
        "sent_at" => "datetime",
    ];

    public function dispatchEntry(): BelongsTo
    {
        return $this->belongsTo(DispatchEntry::class);
    }
}
