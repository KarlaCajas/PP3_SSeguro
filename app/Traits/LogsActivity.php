<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    /**
     * Boot the trait
     */
    public static function bootLogsActivity()
    {
        static::created(function ($model) {
            $model->logActivity('create', null, $model->toArray());
        });

        static::updated(function ($model) {
            $model->logActivity('update', $model->getOriginal(), $model->getChanges());
        });

        static::deleted(function ($model) {
            if ($model->isForceDeleting()) {
                $model->logActivity('hard_delete', $model->toArray(), null, $model->deletion_reason ?? null);
            } else {
                $model->logActivity('soft_delete', $model->toArray(), null, $model->deletion_reason ?? null);
            }
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                $model->logActivity('restore', null, $model->toArray());
            });
        }
    }

    /**
     * Log an activity for this model
     */
    public function logActivity($action, $oldValues = null, $newValues = null, $reason = null)
    {
        if (!Auth::check()) {
            return;
        }

        ActivityLog::create([
            'action' => $action,
            'model_type' => get_class($this),
            'model_id' => $this->id,
            'user_id' => Auth::id(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'reason' => $reason,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Get activity logs for this model
     */
    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'model', 'model_type', 'model_id');
    }
}
