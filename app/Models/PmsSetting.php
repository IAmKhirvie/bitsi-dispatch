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
        "description",
        "is_default",
    ];

    protected $casts = [
        "unit" => PmsUnit::class,
        "threshold" => "integer",
        "is_default" => "boolean",
    ];
}
