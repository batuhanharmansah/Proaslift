<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\IssueReport;
use App\Models\MaintenanceSchedule;
use App\Models\Receivable;
use Illuminate\Http\Request;

/**
 * Müşteri Portalı — sadece okuma. Bina yöneticisi kendi binasının bakım
 * geçmişini, açık arızalarını ve alacak/ödeme durumunu görebilir.
 * Her sorgu, oturumdaki account'un building_id'siyle sıkı sıkıya
 * sınırlandırılır — başka bir binanın verisine asla erişilemez.
 */
class PortalController extends Controller
{
    public function dashboard(Request $request)
    {
        $account = $request->attributes->get('portalAccount');
        $building = $account->building;

        $maintenances = MaintenanceSchedule::where('building_id', $building->id)
            ->orderByDesc('scheduled_date')
            ->limit(20)
            ->get();

        $issues = IssueReport::where('building_id', $building->id)
            ->whereNotIn('status', ['tamamlandi', 'iptal_edildi'])
            ->orderByDesc('created_at')
            ->get();

        $receivables = Receivable::where('building_id', $building->id)
            ->orderByDesc('due_date')
            ->limit(20)
            ->get();

        return view('portal.dashboard', compact('building', 'maintenances', 'issues', 'receivables'));
    }
}
