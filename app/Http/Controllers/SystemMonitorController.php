<?php

namespace App\Http\Controllers;

use App\Models\SystemEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SystemMonitorController extends Controller
{
    public function index(Request $request)
    {
        $query = SystemEvent::query()->orderByDesc('created_at');

        if ($request->filled('source')) {
            $query->where('source', $request->string('source'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->string('severity'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date('date_to'));
        }

        if ($request->filled('q')) {
            $query->where('message', 'like', '%' . $request->string('q') . '%');
        }

        $events = $query->paginate(30)->withQueryString();

        $last24h = SystemEvent::where('created_at', '>=', now()->subDay());
        $last7d = SystemEvent::where('created_at', '>=', now()->subDays(7));

        $summary = [
            'critical_24h' => (clone $last24h)->where('severity', 'critical')->count(),
            'warning_24h' => (clone $last24h)->where('severity', 'warning')->count(),
            'web_24h' => (clone $last24h)->where('source', 'web')->count(),
            'mobile_24h' => (clone $last24h)->where('source', 'mobile')->count(),
            'critical_7d' => (clone $last7d)->where('severity', 'critical')->count(),
            'total_7d' => (clone $last7d)->count(),
        ];

        $types = SystemEvent::select('type')->distinct()->orderBy('type')->pluck('type');

        $categoryCounts = SystemEvent::selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        return view('system-monitor.index', compact('events', 'summary', 'types', 'categoryCounts'));
    }

    public function show(SystemEvent $event)
    {
        return response()->json($event);
    }

    /**
     * SSH erişimi olmayan paylaşımlı hosting'lerde (Natro/Plesk) artisan komutlarını
     * çalıştırmanın bir yolu olmadığı için, geçmiş hata içe aktarma komutunu bu
     * sayfadaki bir butondan tetiklenebilir hale getirir. Bu route zaten
     * 'system.monitor' middleware'i ile korunuyor (sadece izinli e-postalar).
     */
    public function importHistory(Request $request)
    {
        Artisan::call('system-events:import-history');
        $importOutput = Artisan::output();

        Artisan::call('system-events:categorize');
        $categorizeOutput = Artisan::output();

        return back()->with('success', trim($importOutput) . "\n" . trim($categorizeOutput));
    }

    public function categorize(Request $request)
    {
        Artisan::call('system-events:categorize');

        return back()->with('success', trim(Artisan::output()));
    }
}
