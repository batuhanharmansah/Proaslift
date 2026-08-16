<?php

namespace App\Services;

use App\Models\Building;
use App\Models\BuildingApprovalToken;
use App\Models\BuildingContact;
use App\Models\MaintenanceReport;
use App\Models\NotificationPreference;
use App\Models\SmsLog;
use App\Services\Sms\SmsGatewayManager;
use App\Support\PhoneNormalizer;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MaintenanceApprovalService
{
    public function __construct(
        private readonly SmsGatewayManager $smsGatewayManager
    ) {
    }

    public function markPendingApproval(MaintenanceReport $report): MaintenanceReport
    {
        $report->update(['approval_status' => 'onay_bekliyor']);

        return $report->fresh();
    }

    public function getOrCreateToken(Building $building): BuildingApprovalToken
    {
        $companyId = $building->company_id;
        $tokenDays = config('sms.maintenance_approval.token_days', 30);
        $expiresAt = now()->addDays($tokenDays);

        $existing = BuildingApprovalToken::where('building_id', $building->id)
            ->where('expires_at', '>', now())
            ->orderByDesc('id')
            ->first();

        if ($existing) {
            $existing->update(['expires_at' => $expiresAt]);

            return $existing->fresh();
        }

        return BuildingApprovalToken::create([
            'company_id' => $companyId,
            'building_id' => $building->id,
            'token' => Str::random(48),
            'expires_at' => $expiresAt,
        ]);
    }

    public function getPendingReportsForBuilding(int $buildingId): Collection
    {
        return MaintenanceReport::query()
            ->where('building_id', $buildingId)
            ->where('approval_status', 'onay_bekliyor')
            ->where('completion_status', 'tamamlandi')
            ->whereHas('maintenanceSchedule', fn ($q) => $q->where('status', 'tamamlandi'))
            ->with(['maintenanceSchedule.assignedEmployee', 'employee'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function countPendingForBuilding(int $buildingId): int
    {
        return $this->getPendingReportsForBuilding($buildingId)->count();
    }

    public function approvalUrl(BuildingApprovalToken $tokenRecord): string
    {
        return rtrim(config('app.url'), '/') . '/onay/' . $tokenRecord->token;
    }

    /**
     * @return array{sent: bool, error?: string, skipped_reason?: string}
     */
    public function sendApprovalSms(Building $building, bool $force = false): array
    {
        $pendingCount = $this->countPendingForBuilding($building->id);
        if ($pendingCount === 0) {
            return ['sent' => false, 'skipped_reason' => 'no_pending'];
        }

        if (!$force && !NotificationPreference::isEnabled($building->company_id, 'maintenance_completed', 'sms')) {
            return ['sent' => false, 'skipped_reason' => 'sms_disabled_by_preference'];
        }

        $contact = $this->resolvePrimaryContact($building);
        if (!$contact) {
            return ['sent' => false, 'skipped_reason' => 'no_contact'];
        }

        $phone = PhoneNormalizer::normalize($contact->phone);
        if (!$phone) {
            return ['sent' => false, 'skipped_reason' => 'invalid_phone'];
        }

        $tokenRecord = $this->getOrCreateToken($building);

        if (!$force && $this->isSmsOnCooldown($tokenRecord)) {
            return ['sent' => false, 'skipped_reason' => 'cooldown'];
        }

        $buildingName = $building->name ?? 'Bina';
        $label = $pendingCount === 1 ? '1 bakim' : "{$pendingCount} bakim";
        $message = sprintf(
            '%s %s: %s onayinizi bekliyor. %s',
            config('sms.originator'),
            $buildingName,
            $label,
            $this->approvalUrl($tokenRecord)
        );

        $gateway = $this->smsGatewayManager->driver();
        $result = $gateway->send($phone, $message);

        SmsLog::create([
            'company_id' => $building->company_id,
            'building_id' => $building->id,
            'phone_masked' => PhoneNormalizer::mask($phone),
            'message_type' => 'maintenance_approval',
            'provider' => $result['provider'],
            'status' => $result['success'] ? 'sent' : 'failed',
            'error' => $result['error'] ?? null,
        ]);

        if ($result['success']) {
            $tokenRecord->update(['last_sms_sent_at' => now()]);
        }

        return [
            'sent' => $result['success'],
            'error' => $result['error'] ?? null,
        ];
    }

    /**
     * After report is stored with completion_status tamamlandi.
     *
     * @return array{sent: bool, error?: string, skipped_reason?: string}
     */
    public function initiateApprovalFlow(MaintenanceReport $report, bool $forceSms = false): array
    {
        if ($report->completion_status !== 'tamamlandi') {
            return ['sent' => false, 'skipped_reason' => 'not_completed'];
        }

        $this->markPendingApproval($report);

        $report->loadMissing(['building', 'maintenanceSchedule.building']);

        $building = $report->building
            ?? $report->maintenanceSchedule?->building
            ?? ($report->building_id ? Building::find($report->building_id) : null);

        if (!$building) {
            return ['sent' => false, 'skipped_reason' => 'no_building'];
        }

        if (!$report->building_id) {
            $report->update([
                'building_id' => $building->id,
                'company_id' => $report->company_id ?? $building->company_id,
            ]);
        }

        return $this->sendApprovalSms($building, $forceSms);
    }

    public function findValidToken(string $token): ?BuildingApprovalToken
    {
        $record = BuildingApprovalToken::where('token', $token)->first();

        if (!$record || $record->isExpired()) {
            return null;
        }

        return $record;
    }

    /**
     * @return array{approved_count: int, building: Building}
     */
    public function approveAllPending(string $token, string $approvedByName, ?string $ip = null): array
    {
        $tokenRecord = $this->findValidToken($token);
        if (!$tokenRecord) {
            throw new \InvalidArgumentException('Geçersiz veya süresi dolmuş onay bağlantısı.');
        }

        $building = $tokenRecord->building;
        $pending = $this->getPendingReportsForBuilding($building->id);

        if ($pending->isEmpty()) {
            return ['approved_count' => 0, 'building' => $building];
        }

        $approvedCount = 0;

        DB::transaction(function () use ($pending, $approvedByName, $ip, &$approvedCount) {
            $now = now();

            foreach ($pending as $report) {
                $report->update([
                    'approval_status' => 'onaylandi',
                    'approved_by_name' => $approvedByName,
                    'approved_at' => $now,
                    'approval_ip' => $ip,
                    'customer_signature' => true,
                    'customer_name' => $approvedByName,
                ]);
                $approvedCount++;
            }
        });

        return ['approved_count' => $approvedCount, 'building' => $building];
    }

    public function formatReportForApprovalList(MaintenanceReport $report): array
    {
        $schedule = $report->maintenanceSchedule;
        $employeeName = $report->employee?->full_name
            ?? $schedule?->assignedEmployee?->full_name
            ?? '—';

        $description = $report->work_description ?? '';
        if (strlen($description) > 120) {
            $description = substr($description, 0, 117) . '...';
        }

        return [
            'id' => $report->id,
            'maintenance_schedule_id' => $report->maintenance_schedule_id,
            'maintenance_type_label' => $schedule?->maintenance_type_label ?? 'Bakım',
            'scheduled_date' => $schedule?->scheduled_date?->format('d.m.Y') ?? '—',
            'employee_name' => $employeeName,
            'work_description' => $description,
            'total_cost' => $report->total_cost ? (float) $report->total_cost : null,
        ];
    }

    private function resolvePrimaryContact(Building $building): ?BuildingContact
    {
        $primary = $building->primaryContact;
        if ($primary && $primary->is_active && $primary->phone) {
            return $primary;
        }

        return $building->contacts()
            ->where('is_active', true)
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->orderByDesc('is_primary')
            ->first();
    }

    private function isSmsOnCooldown(BuildingApprovalToken $tokenRecord): bool
    {
        if (!$tokenRecord->last_sms_sent_at) {
            return false;
        }

        $hours = config('sms.maintenance_approval.sms_cooldown_hours', 24);

        return $tokenRecord->last_sms_sent_at->greaterThan(
            Carbon::now()->subHours($hours)
        );
    }
}
