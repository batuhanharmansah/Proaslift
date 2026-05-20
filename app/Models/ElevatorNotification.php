<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElevatorNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'building_id',
        'elevator_label_id',
        'type',
        'recipient',
        'recipient_name',
        'subject',
        'content',
        'template_data',
        'status',
        'sent_at',
        'error_message',
        'retry_count',
        'notification_type',
        'days_remaining',
        'escalation_level',
    ];

    protected $casts = [
        'template_data' => 'array',
        'sent_at' => 'datetime',
    ];

    const TYPES = ['eposta', 'sms', 'push', 'webhook'];
    const STATUSES = ['gonderildi', 'basarisiz', 'bekliyor'];
    const NOTIFICATION_TYPES = ['uyari', 'eskalasyon', 'gecikme', 'muhurlenme'];

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

    /**
     * Accessor metodları
     */
    public function getTypeTextAttribute()
    {
        $types = [
            'eposta' => 'E-posta',
            'sms' => 'SMS',
            'push' => 'Push Bildirim',
            'webhook' => 'Webhook',
        ];
        return $types[$this->type] ?? $this->type;
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'gonderildi' => 'Gönderildi',
            'basarisiz' => 'Başarısız',
            'bekliyor' => 'Bekliyor',
        ];
        return $statuses[$this->status] ?? $this->status;
    }

    public function getNotificationTypeTextAttribute()
    {
        $types = [
            'uyari' => 'Uyarı',
            'eskalasyon' => 'Eskalasyon',
            'gecikme' => 'Gecikme',
            'muhurlenme' => 'Mühürlenme',
        ];
        return $types[$this->notification_type] ?? $this->notification_type;
    }

    /**
     * Scope metodları
     */
    public function scopePending($query)
    {
        return $query->where('status', 'bekliyor');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'basarisiz');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'gonderildi');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRetryable($query)
    {
        return $query->where('status', 'basarisiz')
                    ->where('retry_count', '<', 3);
    }

    /**
     * Bildirim gönderme metodları
     */
    public function send()
    {
        try {
            switch ($this->type) {
                case 'eposta':
                    return $this->sendEmail();
                case 'sms':
                    return $this->sendSms();
                case 'push':
                    return $this->sendPush();
                case 'webhook':
                    return $this->sendWebhook();
                default:
                    throw new \Exception('Desteklenmeyen bildirim türü: ' . $this->type);
            }
        } catch (\Exception $e) {
            $this->markAsFailed($e->getMessage());
            return false;
        }
    }

    private function sendEmail()
    {
        // Mail gönderme işlemi
        // Laravel Mail facade kullanılabilir

        $this->markAsSent();
        return true;
    }

    private function sendSms()
    {
        // SMS gönderme işlemi
        // SMS servis entegrasyonu

        $this->markAsSent();
        return true;
    }

    private function sendPush()
    {
        // Push notification gönderme

        $this->markAsSent();
        return true;
    }

    private function sendWebhook()
    {
        // Webhook çağrısı

        $this->markAsSent();
        return true;
    }

    public function markAsSent()
    {
        $this->update([
            'status' => 'gonderildi',
            'sent_at' => now(),
            'error_message' => null,
        ]);

        // Audit log
        AuditLog::create([
            'object_type' => 'ElevatorNotification',
            'object_id' => $this->id,
            'action' => 'sent',
            'description' => 'Bildirim gönderildi',
            'service_name' => 'notification_service',
        ]);
    }

    public function markAsFailed($errorMessage)
    {
        $this->update([
            'status' => 'basarisiz',
            'error_message' => $errorMessage,
            'retry_count' => $this->retry_count + 1,
        ]);

        // Audit log
        AuditLog::create([
            'object_type' => 'ElevatorNotification',
            'object_id' => $this->id,
            'action' => 'failed',
            'description' => 'Bildirim gönderimi başarısız: ' . $errorMessage,
            'service_name' => 'notification_service',
        ]);
    }

    public function retry()
    {
        if ($this->retry_count >= 3) {
            return false;
        }

        $this->update([
            'status' => 'bekliyor',
            'error_message' => null,
        ]);

        return $this->send();
    }

    /**
     * Şablon metodları
     */
    public static function createWarningNotification($elevatorLabel, $daysRemaining)
    {
        $building = $elevatorLabel->building;

        $subject = "[ETIKET UYARISI] {$building->name} - {$building->elevator_code} - " .
                  strtoupper($elevatorLabel->label_color);

        $content = "{$elevatorLabel->label_color_text} etiketli {$building->elevator_code} için " .
                  "takip süresi {$elevatorLabel->due_date->format('d.m.Y')} tarihine kadardır. " .
                  "Kalan gün: {$daysRemaining}. " .
                  "Lütfen düzeltme ve takip kontrolünü planlayınız.";

        return self::create([
            'building_id' => $elevatorLabel->building_id,
            'elevator_label_id' => $elevatorLabel->id,
            'type' => 'eposta',
            'recipient' => $building->responsible_email,
            'recipient_name' => $building->responsible_person,
            'subject' => $subject,
            'content' => $content,
            'notification_type' => 'uyari',
            'days_remaining' => $daysRemaining,
            'template_data' => [
                'building_name' => $building->name,
                'elevator_code' => $building->elevator_code,
                'label_color' => $elevatorLabel->label_color,
                'due_date' => $elevatorLabel->due_date->format('d.m.Y'),
                'days_remaining' => $daysRemaining,
            ],
        ]);
    }

    public static function createOverdueNotification($elevatorLabel, $daysOverdue)
    {
        $building = $elevatorLabel->building;

        $subject = "[GECIKME UYARISI] {$building->name} - {$building->elevator_code} - " .
                  strtoupper($elevatorLabel->label_color);

        $content = "Takip süresi doldu (T+{$daysOverdue}). " .
                  "{$elevatorLabel->label_color_text} etiketli {$building->elevator_code} için " .
                  "gerekli düzeltmeler yapılmamıştır. " .
                  "Lütfen acil aksiyonlar alınız.";

        return self::create([
            'building_id' => $elevatorLabel->building_id,
            'elevator_label_id' => $elevatorLabel->id,
            'type' => 'eposta',
            'recipient' => $building->responsible_email,
            'recipient_name' => $building->responsible_person,
            'subject' => $subject,
            'content' => $content,
            'notification_type' => 'gecikme',
            'days_remaining' => -$daysOverdue,
            'template_data' => [
                'building_name' => $building->name,
                'elevator_code' => $building->elevator_code,
                'label_color' => $elevatorLabel->label_color,
                'days_overdue' => $daysOverdue,
            ],
        ]);
    }

    public static function createEscalationNotification($elevatorLabel, $escalationLevel, $daysOverdue)
    {
        $building = $elevatorLabel->building;

        $subject = "[ESKALASYON SEVİYE {$escalationLevel}] {$building->name} - {$building->elevator_code}";

        $content = "Eskalasyon seviye {$escalationLevel} başlatıldı. " .
                  "Takip süresi doldu (T+{$daysOverdue}). " .
                  "{$elevatorLabel->label_color_text} etiketli {$building->elevator_code} için " .
                  "acil aksiyonlar gereklidir.";

        return self::create([
            'building_id' => $elevatorLabel->building_id,
            'elevator_label_id' => $elevatorLabel->id,
            'type' => 'eposta',
            'recipient' => $building->responsible_email,
            'recipient_name' => $building->responsible_person,
            'subject' => $subject,
            'content' => $content,
            'notification_type' => 'eskalasyon',
            'escalation_level' => $escalationLevel,
            'days_remaining' => -$daysOverdue,
            'template_data' => [
                'building_name' => $building->name,
                'elevator_code' => $building->elevator_code,
                'label_color' => $elevatorLabel->label_color,
                'escalation_level' => $escalationLevel,
                'days_overdue' => $daysOverdue,
            ],
        ]);
    }
}
