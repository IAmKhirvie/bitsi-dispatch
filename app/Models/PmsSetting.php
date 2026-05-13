<?php

namespace App\Models;

use App\Enums\PmsUnit;
use Illuminate\Database\Eloquent\Model;

class PmsSetting extends Model
{
    protected $fillable = [
        "name",
        "unit",
        "threshold",
        "warning_ratio",
        "description",
        "is_default",
    ];

    protected $casts = [
        "unit" => PmsUnit::class,
        "threshold" => "integer",
        "warning_ratio" => "float",
        "is_default" => "boolean",
    ];
}
