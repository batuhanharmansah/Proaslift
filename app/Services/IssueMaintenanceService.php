<?php

namespace App\Services;

use App\Models\IssueReport;
use App\Models\MaintenanceSchedule;

class IssueMaintenanceService
{
    public function ensureMaintenanceSchedule(IssueReport $issue): MaintenanceSchedule
    {
        $issue->loadMissing('maintenanceSchedule');

        if ($issue->maintenanceSchedule) {
            return $issue->maintenanceSchedule;
        }

        $schedule = MaintenanceSchedule::create([
            'company_id' => $issue->company_id,
            'building_id' => $issue->building_id,
            'issue_report_id' => $issue->id,
            'assigned_employee_id' => $issue->assigned_employee_id,
            'maintenance_type' => 'ariza_onarim',
            'title' => "Arıza Onarımı - {$issue->issue_type_label}",
            'scheduled_date' => now()->toDateString(),
            'scheduled_time' => now()->format('H:i'),
            'priority' => $this->mapIssuePriorityToMaintenancePriority($issue->priority),
            'status' => $issue->assigned_employee_id ? 'atandi' : 'planli',
            'description' => $issue->description,
            'notes' => "Arıza bildirimi #{$issue->id} üzerinden otomatik oluşturuldu.",
            'estimated_duration' => 120,
        ]);

        $issue->update([
            'status' => $issue->assigned_employee_id ? 'ekip_atandi' : 'inceleniyor',
        ]);

        return $schedule;
    }

    public function syncMaintenanceAssignment(IssueReport $issue): ?MaintenanceSchedule
    {
        $maintenance = $issue->maintenanceSchedule;

        if (!$maintenance) {
            return null;
        }

        $maintenance->update([
            'assigned_employee_id' => $issue->assigned_employee_id,
            'status' => $issue->assigned_employee_id ? 'atandi' : 'planli',
        ]);

        return $maintenance->fresh();
    }

    public function syncMaintenanceStarted(IssueReport $issue): ?MaintenanceSchedule
    {
        $maintenance = $issue->maintenanceSchedule;

        if (!$maintenance) {
            return null;
        }

        if (in_array($maintenance->status, ['planli', 'atandi'], true)) {
            $maintenance->update(['status' => 'baslandi']);
        }

        return $maintenance->fresh();
    }

    public function syncMaintenanceCompleted(IssueReport $issue): ?MaintenanceSchedule
    {
        $maintenance = $issue->maintenanceSchedule;

        if (!$maintenance) {
            return null;
        }

        if ($maintenance->status !== 'tamamlandi') {
            $maintenance->update(['status' => 'tamamlandi']);
        }

        return $maintenance->fresh();
    }

    private function mapIssuePriorityToMaintenancePriority(string $priority): string
    {
        return match ($priority) {
            'acil' => 'acil',
            'yuksek' => 'yuksek',
            'dusuk' => 'dusuk',
            default => 'normal',
        };
    }
}
