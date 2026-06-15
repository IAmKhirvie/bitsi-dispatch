<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ReportTemplate extends Model
{
    protected $fillable = [
        'report_type',
        'file_name',
        'file_path',
        'uploaded_by',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public static function active(string $reportType): ?self
    {
        return self::where('report_type', $reportType)->latest('id')->first();
    }

    protected static function booted(): void
    {
        static::deleting(function (ReportTemplate $template) {
            Storage::disk('local')->delete($template->file_path);
        });
    }
}
