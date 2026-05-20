<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 🔔 ENTERPRISE NOTIFICATION MODEL
 * Kullanıcı bildirimleri - Bakım, Arıza, Finansal, Personel, Sistem, Genel
 */
class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'type',
        'priority',
        'title',
        'body',
        'data',
        'related_entity_type',
        'related_entity_id',
        'read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ==================== RELATIONSHIPS ====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // ==================== SCOPES ====================

    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('read', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // ==================== ACCESSORS ====================

    public function getTypeLabelAttribute(): string
    {
        $labels = [
            'maintenance' => 'Bakım',
            'issue' => 'Arıza',
            'financial' => 'Finansal',
            'employee' => 'Personel',
            'system' => 'Sistem',
            'general' => 'Genel',
        ];

        return $labels[$this->type] ?? $this->type;
    }

    public function getPriorityLabelAttribute(): string
    {
        $labels = [
            'critical' => 'Kritik',
            'high' => 'Yüksek',
            'medium' => 'Orta',
            'low' => 'Düşük',
        ];

        return $labels[$this->priority] ?? $this->priority;
    }

    // ==================== METHODS ====================

    /**
     * Bildirimi okundu olarak işaretle
     */
    public function markAsRead(): void
    {
        if (!$this->read) {
            $this->update([
                'read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Bildirimi okunmadı olarak işaretle
     */
    public function markAsUnread(): void
    {
        if ($this->read) {
            $this->update([
                'read' => false,
                'read_at' => null,
            ]);
        }
    }

    /**
     * Bildirim oluştur (static helper)
     */
    public static function createNotification(array $data): self
    {
        return self::create([
            'user_id' => $data['user_id'],
            'company_id' => $data['company_id'],
            'type' => $data['type'],
            'priority' => $data['priority'] ?? 'medium',
            'title' => $data['title'],
            'body' => $data['body'],
            'data' => $data['data'] ?? null,
            'related_entity_type' => $data['related_entity_type'] ?? null,
            'related_entity_id' => $data['related_entity_id'] ?? null,
        ]);
    }

    /**
     * Toplu bildirim oluştur (şirket içindeki tüm kullanıcılara)
     */
    public static function createForCompany(int $companyId, array $data): void
    {
        $users = User::where('company_id', $companyId)->get();

        foreach ($users as $user) {
            self::createNotification([
                'user_id' => $user->id,
                'company_id' => $companyId,
                'type' => $data['type'],
                'priority' => $data['priority'] ?? 'medium',
                'title' => $data['title'],
                'body' => $data['body'],
                'data' => $data['data'] ?? null,
                'related_entity_type' => $data['related_entity_type'] ?? null,
                'related_entity_id' => $data['related_entity_id'] ?? null,
            ]);
        }
    }
}
