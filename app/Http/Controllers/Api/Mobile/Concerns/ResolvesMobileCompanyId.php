<?php

namespace App\Http\Controllers\Api\Mobile\Concerns;

use App\Models\Employee;
use Illuminate\Http\Request;

/**
 * Mobil API: users.company_id boş olsa bile employee kaydından firma çözümleme
 * (BuildingController / DashboardController ile aynı mantık).
 */
trait ResolvesMobileCompanyId
{
    protected function resolveMobileCompanyId(Request $request): ?int
    {
        $user = $request->user();
        $companyId = $user->company_id;

        if (!$companyId) {
            $employee = $user->employee ?? Employee::where('email', $user->email)->first();
            $companyId = $employee?->company_id;
        }

        return $companyId !== null ? (int) $companyId : null;
    }
}
