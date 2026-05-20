<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElevatorEscalation extends Model
{
    use HasFactory;

    protected $fillable = [
        'building_id',
        'elevator_label_id',
        'level',
        'status',
        'description',
        'triggered_at',
        'days_overdue',
        'original_due_date',
        'resolved_at',
        'resolution_notes',
        'resolved_by',
        'notified_parties',
        'actions_taken',
        'created_by',
    ];

    protected $casts = [
        'triggered_at' => 'datetime',
        'original_due_date' => 'date',
        'resolved_at' => 'datetime',
        'notified_parties' => 'array',
        'actions_taken' => 'array',
    ];

    const STATUSES = ['aktif', 'cozuldu', 'iptal'];

    // Eskalasyon seviyeleri ve politikaları
    const ESCALATION_POLICIES = [
        1 => [
            'trigger_days' => 1,
            'name' => 'Seviye 1',
            'description' => 'Bina yöneticisi + Servis firması',
            'recipients' => ['building_manager', 'service_company'],
        ],
        2 => [
            'trigger_days' => 7,
            'name' => 'Seviye 2',
            'description' => 'Bina yöneticisi + Site yönetimi + Belediye (opsiyonel)',
            'recipients' => ['building_manager', 'site_management', 'municipality'],
        ],
        3 => [
            'trigger_days' => 15,
            'name' => 'Seviye 3',
            'description' => 'Hukuki/Cezai süreç bilgilendirme',
            'recipients' => ['legal_department', 'municipality', 'regulatory_authority'],
        ],
    ];

    /**
     * İlişkiler
     */
    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function elevatorLabel()
    {
        return $this->belongsTo(ElevatorLabel::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Accessor metodları
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            'aktif' => 'Aktif',
            'cozuldu' => 'Çözüldü',
            'iptal' => 'İptal',
        ];
        return $statuses[$this->status] ?? $this->status;
    }

    public function getLevelNameAttribute()
    {
        return self::ESCALATION_POLICIES[$this->level]['name'] ?? "Seviye {$this->level}";
    }

    public function getLevelDescriptionAttribute()
    {
        return self::ESCALATION_POLICIES[$this->level]['description'] ?? '';
    }

    /**
     * Scope metodları
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'cozuldu');
    }

    /**
     * Eskalasyon oluşturma metodları
     */
    public static function createEscalation($elevatorLabel, $level, $daysOverdue)
    {
        $escalation = self::create([
            'building_id' => $elevatorLabel->building_id,
            'elevator_label_id' => $elevatorLabel->id,
            'level' => $level,
            'status' => 'aktif',
            'description' => "Seviye {$level} eskalasyon - {$daysOverdue} gün gecikme",
            'triggered_at' => now(),
            'days_overdue' => $daysOverdue,
            'original_due_date' => $elevatorLabel->due_date,
            'created_by' => null, // System tarafından oluşturulan
        ]);

        // Bildirim gönder
        $escalation->sendNotifications();

        // Audit log
        AuditLog::create([
            'object_type' => 'ElevatorEscalation',
            'object_id' => $escalation->id,
            'action' => 'created',
            'description' => "Seviye {$level} eskalasyon oluşturuldu - {$daysOverdue} gün gecikme",
            'service_name' => 'escalation_service',
            'new_values' => $escalation->toArray(),
        ]);

        return $escalation;
    }

    /**
     * Bildirim gönderme
     */
    public function sendNotifications()
    {
        $building = $this->building;
        $policy = self::ESCALATION_POLICIES[$this->level] ?? [];
        $recipients = $policy['recipients'] ?? [];

        $notifiedParties = [];
        $actions = [];

        foreach ($recipients as $recipientType) {
            $recipient = $this->getRecipientInfo($recipientType);

            if ($recipient) {
                $notification = ElevatorNotification::createEscalationNotification(
                    $this->elevatorLabel,
                    $this->level,
                    $this->days_overdue
                );

                $notification->update([
                    'recipient' => $recipient['email'],
                    'recipient_name' => $recipient['name'],
                ]);

                $notification->send();

                $notifiedParties[] = [
                    'type' => $recipientType,
                    'name' => $recipient['name'],
                    'email' => $recipient['email'],
                    'notified_at' => now(),
                ];

                $actions[] = [
                    'action' => 'notification_sent',
                    'recipient_type' => $recipientType,
                    'timestamp' => now(),
                ];
            }
        }

        $this->update([
            'notified_parties' => $notifiedParties,
            'actions_taken' => $actions,
        ]);
    }

    private function getRecipientInfo($recipientType)
    {
        $building = $this->building;

        switch ($recipientType) {
            case 'building_manager':
                return [
                    'name' => $building->responsible_person,
                    'email' => $building->responsible_email,
                ];

            case 'service_company':
                // Servis firması bilgileri (company tablosundan alınabilir)
                return [
                    'name' => 'Servis Firması',
                    'email' => 'servis@example.com', // Config'den alınmalı
                ];

            case 'site_management':
                // Site yönetimi bilgileri
                return [
                    'name' => 'Site Yönetimi',
                    'email' => 'yonetim@example.com', // Config'den alınmalı
                ];

            case 'municipality':
                // Belediye bilgileri
                return [
                    'name' => 'Belediye',
                    'email' => 'belediye@example.com', // Config'den alınmalı
                ];

            case 'legal_department':
                // Hukuk departmanı
                return [
                    'name' => 'Hukuk Departmanı',
                    'email' => 'hukuk@example.com', // Config'den alınmalı
                ];

            case 'regulatory_authority':
                // Düzenleyici otorite
                return [
                    'name' => 'Düzenleyici Otorite',
                    'email' => 'otorite@example.com', // Config'den alınmalı
                ];

            default:
                return null;
        }
    }

    /**
     * Eskalasyon çözme
     */
    public function resolve($notes = null, $userId = null)
    {
        $this->update([
            'status' => 'cozuldu',
            'resolved_at' => now(),
            'resolution_notes' => $notes,
            'resolved_by' => $userId,
        ]);

        // Aksiyonları güncelle
        $actions = $this->actions_taken ?? [];
        $actions[] = [
            'action' => 'resolved',
            'notes' => $notes,
            'resolved_by' => $userId,
            'timestamp' => now(),
        ];

        $this->update(['actions_taken' => $actions]);

        // Audit log
        AuditLog::create([
            'object_type' => 'ElevatorEscalation',
            'object_id' => $this->id,
            'action' => 'resolved',
            'description' => 'Eskalasyon çözüldü: ' . $notes,
            'user_id' => $userId,
            'old_values' => ['status' => 'aktif'],
            'new_values' => ['status' => 'cozuldu'],
        ]);
    }

    /**
     * Eskalasyon iptal etme
     */
    public function cancel($reason = null, $userId = null)
    {
        $this->update([
            'status' => 'iptal',
            'resolution_notes' => $reason,
            'resolved_by' => $userId,
            'resolved_at' => now(),
        ]);

        // Audit log
        AuditLog::create([
            'object_type' => 'ElevatorEscalation',
            'object_id' => $this->id,
            'action' => 'cancelled',
            'description' => 'Eskalasyon iptal edildi: ' . $reason,
            'user_id' => $userId,
            'old_values' => ['status' => 'aktif'],
            'new_values' => ['status' => 'iptal'],
        ]);
    }

    /**
     * Bir sonraki eskalasyon seviyesini tetikleme
     */
    public function triggerNextLevel()
    {
        $nextLevel = $this->level + 1;

        if (isset(self::ESCALATION_POLICIES[$nextLevel])) {
            return self::createEscalation(
                $this->elevatorLabel,
                $nextLevel,
                $this->days_overdue + 1
            );
        }

        return null;
    }

    /**
     * Eskalasyon geçmişi
     */
    public static function getEscalationHistory($elevatorLabelId)
    {
        return self::where('elevator_label_id', $elevatorLabelId)
                  ->orderBy('level')
                  ->orderBy('triggered_at')
                  ->get();
    }
}
