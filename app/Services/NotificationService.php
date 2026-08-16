<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\MaintenanceSchedule;
use App\Models\IssueReport;
use App\Models\Receivable;
use App\Models\Payable;
use App\Models\NotificationPreference;
use Carbon\Carbon;

/**
 * 🔔 ENTERPRISE NOTIFICATION SERVICE
 * Otomatik bildirim oluşturma servisi
 */
class NotificationService
{
    public function __construct(
        private readonly PushNotificationService $pushNotificationService
    ) {
    }

    /**
     * Bakım planlandığında bildirim oluştur
     */
    public function notifyMaintenanceScheduled(MaintenanceSchedule $maintenance): void
    {
        $building = $maintenance->building;
        $companyId = $maintenance->company_id;

        // Admin kullanıcılarına bildirim gönder (company_admin veya super_admin rolüne sahip)
        $users = User::where('company_id', $companyId)
            ->whereHas('userRoles', function ($q) use ($companyId) {
                $q->where('company_id', $companyId)
                    ->where('is_active', true)
                    ->whereHas('role', function ($q2) {
                        $q2->whereIn('slug', ['company_admin', 'super_admin']);
                    });
            })
            ->get();

        $this->notifyUsers($users, $companyId, [
            'type' => 'maintenance',
            'event_key' => 'maintenance_scheduled',
            'priority' => $this->mapPriorityToNotificationPriority($maintenance->priority),
            'title' => 'Yeni Bakım Planlandı',
            'body' => "{$building->name} için {$maintenance->maintenance_type_label} planlandı. Tarih: " . $maintenance->scheduled_date->format('d.m.Y'),
            'data' => [
                'screen' => 'MaintenanceDetail',
                'params' => ['maintenanceId' => $maintenance->id],
            ],
            'related_entity_type' => 'maintenance',
            'related_entity_id' => $maintenance->id,
        ]);

        // Atanan personele bildirim gönder
        if ($maintenance->assigned_employee_id) {
            $employee = $maintenance->assignedEmployee;
            if ($employee && $employee->user_id) {
                $this->notifyUsers(collect([$employee->user]), $companyId, [
                    'type' => 'employee',
                    'event_key' => 'maintenance_assigned',
                    'priority' => $this->mapPriorityToNotificationPriority($maintenance->priority),
                    'title' => 'Yeni İş Atandı',
                    'body' => "{$building->name} için bakım işi size atandı. Tarih: " . $maintenance->scheduled_date->format('d.m.Y'),
                    'data' => [
                        'screen' => 'MaintenanceDetail',
                        'params' => ['maintenanceId' => $maintenance->id],
                    ],
                    'related_entity_type' => 'maintenance',
                    'related_entity_id' => $maintenance->id,
                ]);
            }
        }
    }

    /**
     * Bakım 24 saat öncesi hatırlatma
     */
    public function notifyMaintenanceReminder(MaintenanceSchedule $maintenance): void
    {
        $building = $maintenance->building;
        $companyId = $maintenance->company_id;

        // Admin kullanıcılarına bildirim gönder (company_admin veya super_admin rolüne sahip)
        $users = User::where('company_id', $companyId)
            ->whereHas('userRoles', function ($q) use ($companyId) {
                $q->where('company_id', $companyId)
                    ->where('is_active', true)
                    ->whereHas('role', function ($q2) {
                        $q2->whereIn('slug', ['company_admin', 'super_admin']);
                    });
            })
            ->get();

        $this->notifyUsers($users, $companyId, [
            'type' => 'maintenance',
            'event_key' => 'maintenance_reminder',
            'priority' => 'high',
            'title' => 'Bakım Hatırlatması',
            'body' => "{$building->name} için yarın bakım planlandı. Tarih: " . $maintenance->scheduled_date->format('d.m.Y'),
            'data' => [
                'screen' => 'MaintenanceDetail',
                'params' => ['maintenanceId' => $maintenance->id],
            ],
            'related_entity_type' => 'maintenance',
            'related_entity_id' => $maintenance->id,
        ]);

        // Atanan personele bildirim gönder
        if ($maintenance->assigned_employee_id) {
            $employee = $maintenance->assignedEmployee;
            if ($employee && $employee->user_id) {
                $this->notifyUsers(collect([$employee->user]), $companyId, [
                    'type' => 'employee',
                    'event_key' => 'maintenance_reminder',
                    'priority' => 'high',
                    'title' => 'Bakım Hatırlatması',
                    'body' => "{$building->name} için yarın bakım yapmanız gerekiyor. Tarih: " . $maintenance->scheduled_date->format('d.m.Y'),
                    'data' => [
                        'screen' => 'MaintenanceDetail',
                        'params' => ['maintenanceId' => $maintenance->id],
                    ],
                    'related_entity_type' => 'maintenance',
                    'related_entity_id' => $maintenance->id,
                ]);
            }
        }
    }

    /**
     * Bakım tamamlandığında bildirim
     */
    public function notifyMaintenanceCompleted(MaintenanceSchedule $maintenance): void
    {
        $building = $maintenance->building;
        $companyId = $maintenance->company_id;

        // Admin kullanıcılarına bildirim gönder
        $this->notifyCompanyUsers($companyId, [
            'type' => 'maintenance',
            'event_key' => 'maintenance_completed',
            'priority' => 'medium',
            'title' => 'Bakım Tamamlandı',
            'body' => "{$building->name} için {$maintenance->maintenance_type_label} tamamlandı.",
            'data' => [
                'screen' => 'MaintenanceDetail',
                'params' => ['maintenanceId' => $maintenance->id],
            ],
            'related_entity_type' => 'maintenance',
            'related_entity_id' => $maintenance->id,
        ]);
    }

    /**
     * Arıza bildirimi oluşturulduğunda
     */
    public function notifyIssueReported(IssueReport $issue): void
    {
        $building = $issue->building;
        $companyId = $issue->company_id;

        $priority = $issue->is_urgent ? 'critical' : 'high';

        // Admin kullanıcılarına bildirim gönder
        $this->notifyCompanyUsers($companyId, [
            'type' => 'issue',
            'event_key' => 'issue_reported',
            'priority' => $priority,
            'title' => $issue->is_urgent ? 'Acil Arıza Bildirimi' : 'Arıza Bildirimi',
            'body' => "{$building->name} için arıza bildirildi: {$issue->description}",
            'data' => [
                'screen' => 'IssueDetail',
                'params' => ['issueId' => $issue->id],
            ],
            'related_entity_type' => 'issue',
            'related_entity_id' => $issue->id,
        ]);
    }

    /**
     * Ödeme vadesi yaklaşıyor (3 gün öncesi)
     */
    public function notifyPaymentDueSoon(Receivable $receivable): void
    {
        $building = $receivable->building;
        $companyId = $receivable->company_id;
        $daysUntilDue = Carbon::parse($receivable->due_date)->diffInDays(now(), false);

        $this->notifyCompanyUsers($companyId, [
            'type' => 'financial',
            'event_key' => 'payment_due_soon',
            'priority' => $daysUntilDue <= 1 ? 'critical' : 'high',
            'title' => 'Ödeme Vadesi Yaklaşıyor',
            'body' => "{$building->name} için {$receivable->total_amount} TL ödeme {$daysUntilDue} gün sonra vadesi dolacak.",
            'data' => [
                'screen' => 'Receivables',
            ],
            'related_entity_type' => 'receivable',
            'related_entity_id' => $receivable->id,
        ]);
    }

    /**
     * Ödeme gecikti
     */
    public function notifyPaymentOverdue(Receivable $receivable): void
    {
        $building = $receivable->building;
        $companyId = $receivable->company_id;
        $daysOverdue = Carbon::parse($receivable->due_date)->diffInDays(now(), false);

        $this->notifyCompanyUsers($companyId, [
            'type' => 'financial',
            'event_key' => 'payment_overdue',
            'priority' => 'critical',
            'title' => 'Ödeme Gecikti',
            'body' => "{$building->name} için {$receivable->total_amount} TL ödeme {$daysOverdue} gün gecikti.",
            'data' => [
                'screen' => 'Receivables',
            ],
            'related_entity_type' => 'receivable',
            'related_entity_id' => $receivable->id,
        ]);
    }

    /**
     * Ödeme alındı
     */
    public function notifyPaymentReceived(Receivable $receivable, float $amount): void
    {
        $building = $receivable->building;
        $companyId = $receivable->company_id;

        $this->notifyCompanyUsers($companyId, [
            'type' => 'financial',
            'event_key' => 'payment_received',
            'priority' => 'medium',
            'title' => 'Ödeme Alındı',
            'body' => "{$building->name} için {$amount} TL ödeme alındı.",
            'data' => [
                'screen' => 'Receivables',
            ],
            'related_entity_type' => 'receivable',
            'related_entity_id' => $receivable->id,
        ]);
    }

    /**
     * Borç vadesi yaklaşıyor
     */
    public function notifyPayableDueSoon(Payable $payable): void
    {
        $companyId = $payable->company_id;
        $daysUntilDue = Carbon::parse($payable->due_date)->diffInDays(now(), false);

        $this->notifyCompanyUsers($companyId, [
            'type' => 'financial',
            'event_key' => 'payable_due_soon',
            'priority' => $daysUntilDue <= 1 ? 'critical' : 'high',
            'title' => 'Borç Vadesi Yaklaşıyor',
            'body' => "{$payable->title} için {$payable->total_amount} TL borç {$daysUntilDue} gün sonra vadesi dolacak.",
            'data' => [
                'screen' => 'Payables',
            ],
            'related_entity_type' => 'payable',
            'related_entity_id' => $payable->id,
        ]);
    }

    /**
     * Priority mapping
     */
    private function mapPriorityToNotificationPriority(string $priority): string
    {
        $mapping = [
            'acil' => 'critical',
            'yuksek' => 'high',
            'normal' => 'medium',
            'dusuk' => 'low',
        ];

        return $mapping[$priority] ?? 'medium';
    }

    private function notifyCompanyUsers(int $companyId, array $data): void
    {
        $users = User::where('company_id', $companyId)->get();
        $this->notifyUsers($users, $companyId, $data);
    }

    private function notifyUsers($users, int $companyId, array $data): void
    {
        $userIds = [];

        foreach ($users as $user) {
            if (!$user) {
                continue;
            }

            $userIds[] = $user->id;

            Notification::createNotification([
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

        // Uygulama içi bildirim (zil) her zaman oluşturulur, kapatılamaz — yukarıda zaten yapıldı.
        // Push bildirimi ise firma bu olay için kapatmış olabilir, göndermeden önce kontrol edilir.
        $eventKey = $data['event_key'] ?? null;
        $pushEnabled = !$eventKey || NotificationPreference::isEnabled($companyId, $eventKey, 'push');

        if ($pushEnabled) {
            $this->pushNotificationService->sendToUsers($userIds, [
                'title' => $data['title'],
                'body' => $data['body'],
                'data' => $data['data'] ?? [],
            ]);
        }
    }
}
