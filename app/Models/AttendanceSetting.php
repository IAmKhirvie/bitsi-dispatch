<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'string',
    ];

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        // Try to decode JSON value
        $value = json_decode($setting->value, true);

        return json_last_error() === JSON_ERROR_NONE ? $value : $setting->value;
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, mixed $value): static
    {
        $stringValue = is_scalar($value) ? (string) $value : json_encode($value);

        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $stringValue]
        );
    }

    /**
     * Get all settings as a key-value array.
     */
    public static function getAll(): array
    {
        return static::all()->pluck('value', 'key')->toArray();
    }

    /**
     * Initialize default settings.
     */
    public static function initializeDefaults(): void
    {
        $defaults = [
            'late_threshold_minutes' => '15',
            'pre_departure_alert_minutes' => '15',
            'auto_absent_timeout_minutes' => '30',
            'require_check_in' => 'true',
        ];

        foreach ($defaults as $key => $value) {
            if (!static::where('key', $key)->exists()) {
                static::create([
                    'key' => $key,
                    'value' => $value,
                ]);
            }
        }
    }
}
