<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            self::logAudit($model, 'created', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $old = array_intersect_key($model->getOriginal(), $model->getDirty());
            $new = $model->getDirty();
            if (! empty($new)) {
                self::logAudit($model, 'updated', $old, $new);
            }
        });

        static::deleted(function ($model) {
            self::logAudit($model, 'deleted', $model->getAttributes(), null);
        });

        if (method_exists(static::class, 'restoring')) {
            static::restored(function ($model) {
                self::logAudit($model, 'restored', null, $model->getAttributes());
            });
        }
    }

    private static function logAudit($model, string $action, ?array $old, ?array $new): void
    {
        if (auth()->check()) {
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'auditable_type' => get_class($model),
                'auditable_id' => $model->id,
                'old_values' => $old,
                'new_values' => $new,
                'ip_address' => request()->ip(),
            ]);
        }
    }
}
