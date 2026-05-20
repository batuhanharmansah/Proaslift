<?php

namespace App\Support;

use App\Models\MaintenanceReport;

class MaintenanceReportApprovalSerializer
{
    public static function forApi(?MaintenanceReport $report): ?array
    {
        if (!$report) {
            return null;
        }

        return [
            'approval_status' => $report->approval_status,
            'approval_status_label' => $report->approval_status_label,
            'approved_by_name' => $report->approved_by_name,
            'approved_at' => $report->approved_at?->format('Y-m-d H:i:s'),
        ];
    }
}
