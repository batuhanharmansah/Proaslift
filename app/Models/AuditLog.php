<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'object_type',
        'object_id',
        'action',
        'description',
        'user_id',
        'user_name',
        'service_name',
        'ip_address',
        'user_agent',
        'old_values',
        'new_values',
        'metadata',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    // Timestamps sadece created_at kullanılır
    public $timestamps = false;

    protected $dates = ['created_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = now();

            // IP ve User Agent bilgilerini otomatik al
            if (request()) {
                $model->ip_address = $model->ip_address ?: request()->ip();
                $model->user_agent = $model->user_agent ?: request()->userAgent();
            }

            // Kullanıcı adını otomatik al
            if ($model->user_id && !$model->user_name) {
                $user = User::find($model->user_id);
                $model->user_name = $user ? $user->name : null;
            }
        });
    }

    /**
     * İlişkiler
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope metodları
     */
    public function scopeForObject($query, $objectType, $objectId)
    {
        return $query->where('object_type', $objectType)
                    ->where('object_id', $objectId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByService($query, $serviceName)
    {
        return $query->where('service_name', $serviceName);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Accessor metodları
     */
    public function getActorAttribute()
    {
        if ($this->user_name) {
            return $this->user_name;
        } elseif ($this->service_name) {
            return "Sistem: {$this->service_name}";
        } else {
            return 'Bilinmeyen';
        }
    }

    /**
     * Static yardımcı metodlar
     */
    public static function logModelChange($model, $action, $description = null, $userId = null)
    {
        $oldValues = $model->getOriginal();
        $newValues = $model->getAttributes();

        // Sadece değişen alanları kaydet
        if ($action === 'updated') {
            $changes = $model->getChanges();
            $newValues = $changes;
            $oldValues = array_intersect_key($oldValues, $changes);
        }

        return self::create([
            'object_type' => get_class($model),
            'object_id' => $model->id,
            'action' => $action,
            'description' => $description ?: self::getDefaultDescription($model, $action),
            'user_id' => $userId ?: auth()->id(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }

    public static function logCustomAction($objectType, $objectId, $action, $description, $metadata = null, $userId = null)
    {
        return self::create([
            'object_type' => $objectType,
            'object_id' => $objectId,
            'action' => $action,
            'description' => $description,
            'user_id' => $userId ?: auth()->id(),
            'metadata' => $metadata,
        ]);
    }

    public static function logSystemAction($action, $description, $serviceName, $metadata = null)
    {
        return self::create([
            'object_type' => 'System',
            'object_id' => 0,
            'action' => $action,
            'description' => $description,
            'service_name' => $serviceName,
            'metadata' => $metadata,
        ]);
    }

    private static function getDefaultDescription($model, $action)
    {
        $modelName = class_basename($model);
        $modelId = $model->id;

        switch ($action) {
            case 'created':
                return "{$modelName} #{$modelId} oluşturuldu";
            case 'updated':
                return "{$modelName} #{$modelId} güncellendi";
            case 'deleted':
                return "{$modelName} #{$modelId} silindi";
            default:
                return "{$modelName} #{$modelId} - {$action}";
        }
    }

    /**
     * Audit log geçmişi alma
     */
    public static function getObjectHistory($objectType, $objectId, $limit = 50)
    {
        return self::forObject($objectType, $objectId)
                  ->orderBy('created_at', 'desc')
                  ->limit($limit)
                  ->get();
    }

    /**
     * Kullanıcı aktivite geçmişi
     */
    public static function getUserActivity($userId, $days = 30, $limit = 100)
    {
        return self::byUser($userId)
                  ->recent($days)
                  ->orderBy('created_at', 'desc')
                  ->limit($limit)
                  ->get();
    }

    /**
     * Sistem aktivite özeti
     */
    public static function getSystemActivity($days = 7)
    {
        $startDate = now()->subDays($days);

        return [
            'total_actions' => self::where('created_at', '>=', $startDate)->count(),
            'user_actions' => self::where('created_at', '>=', $startDate)
                                 ->whereNotNull('user_id')
                                 ->count(),
            'system_actions' => self::where('created_at', '>=', $startDate)
                                   ->whereNotNull('service_name')
                                   ->count(),
            'top_actions' => self::where('created_at', '>=', $startDate)
                                ->selectRaw('action, COUNT(*) as count')
                                ->groupBy('action')
                                ->orderBy('count', 'desc')
                                ->limit(10)
                                ->get(),
            'top_users' => self::where('created_at', '>=', $startDate)
                              ->whereNotNull('user_id')
                              ->selectRaw('user_id, user_name, COUNT(*) as count')
                              ->groupBy('user_id', 'user_name')
                              ->orderBy('count', 'desc')
                              ->limit(10)
                              ->get(),
        ];
    }

    /**
     * Değişiklik detaylarını formatla
     */
    public function getFormattedChanges()
    {
        if (!$this->old_values || !$this->new_values) {
            return [];
        }

        $changes = [];

        foreach ($this->new_values as $field => $newValue) {
            $oldValue = $this->old_values[$field] ?? null;

            if ($oldValue !== $newValue) {
                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }

    /**
     * Değişiklik özetini al
     */
    public function getChangeSummary()
    {
        $changes = $this->getFormattedChanges();

        if (empty($changes)) {
            return 'Değişiklik yok';
        }

        $summary = [];
        foreach ($changes as $field => $change) {
            $summary[] = "{$field}: '{$change['old']}' → '{$change['new']}'";
        }

        return implode(', ', $summary);
    }
}
