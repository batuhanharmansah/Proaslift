<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceSchedule;
use App\Models\Building;
use App\Models\Employee;
use App\Services\MaintenanceApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenanceSchedule::with(['building', 'assignedEmployee', 'maintenanceReport'])->whereHas('building', function($q) {
            $q->where('company_id', auth()->user()->company_id);
        });

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->filled('approval_status')) {
            $query->whereHas('maintenanceReport', function ($q) use ($request) {
                $q->where('approval_status', $request->approval_status);
            });
        }

        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('building_id') && $request->building_id) {
            $query->where('building_id', $request->building_id);
        }

        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('assigned_employee_id', $request->employee_id);
        }

        $maintenances = $query->orderBy('scheduled_date', 'asc')->paginate(10);
        $buildings = Building::where('status', 'aktif')->get();

        return view('maintenance.index', compact('maintenances', 'buildings'));
    }

    public function create()
    {
        $buildings = Building::where('status', 'aktif')->where('company_id', auth()->user()->company_id)->get();
        $employees = Employee::where('is_active', true)->where('company_id', auth()->user()->company_id)->get();

        return view('maintenance.create', compact('buildings', 'employees'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'building_id' => 'required|exists:buildings,id',
            'maintenance_type' => 'required|in:rutin_bakim,ariza_onarim,periyodik_kontrol,modernizasyon',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'scheduled_time' => 'nullable|date_format:H:i',
            'priority' => 'required|in:dusuk,normal,yuksek,acil',
            'description' => 'required|string',
            'assigned_employee_id' => 'nullable|exists:employees,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only([
            'building_id', 'maintenance_type', 'scheduled_date', 'scheduled_time',
            'priority', 'assigned_employee_id', 'description',
        ]);
        $data['company_id'] = auth()->user()->company_id;

        MaintenanceSchedule::create($data);

        return redirect()->route('maintenance.index')->with('success', 'Bakım işi başarıyla planlandı.');
    }

    public function show(MaintenanceSchedule $maintenance)
    {
        // Model bulunamadıysa 404 hatası döndür
        if (!$maintenance->exists) {
            abort(404, 'Bakım kaydı bulunamadı.');
        }

        // Super Admin için company_id kontrolü yapma
        if (auth()->user()->company_id && $maintenance->company_id !== auth()->user()->company_id) {
            abort(403, 'Bu bakım işine erişim yetkiniz yok.');
        }

        $maintenance->load(['building', 'assignedEmployee', 'maintenanceReports', 'maintenanceReport']);

        return view('maintenance.show', compact('maintenance'));
    }

    public function resendApprovalSms(MaintenanceSchedule $maintenance)
    {
        if (auth()->user()->company_id && $maintenance->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $building = $maintenance->building;
        if (!$building) {
            return redirect()->back()->with('error', 'Bina bulunamadı.');
        }

        $result = app(MaintenanceApprovalService::class)->sendApprovalSms($building, true);

        if ($result['sent'] ?? false) {
            return redirect()->back()->with('success', 'Onay SMS\'i gönderildi.');
        }

        $message = match ($result['skipped_reason'] ?? null) {
            'no_pending' => 'Onay bekleyen iş bulunmuyor.',
            'no_contact' => 'Bina için birincil iletişim kişisi tanımlı değil.',
            'invalid_phone' => 'İletişim telefonu geçersiz.',
            default => $result['error'] ?? 'SMS gönderilemedi.',
        };

        return redirect()->back()->with('error', $message);
    }

    public function edit(MaintenanceSchedule $maintenance)
    {
        // Model bulunamadıysa 404 hatası döndür
        if (!$maintenance->exists) {
            abort(404, 'Bakım kaydı bulunamadı.');
        }

        $buildings = Building::where('status', 'aktif')->where('company_id', auth()->user()->company_id)->get();
        $employees = Employee::where('is_active', true)->where('company_id', auth()->user()->company_id)->get();

        return view('maintenance.edit', compact('maintenance', 'buildings', 'employees'));
    }

    public function update(Request $request, MaintenanceSchedule $maintenance)
    {
        // Model bulunamadıysa 404 hatası döndür
        if (!$maintenance->exists) {
            abort(404, 'Bakım kaydı bulunamadı.');
        }

        $validator = Validator::make($request->all(), [
            'building_id' => 'required|exists:buildings,id',
            'assigned_employee_id' => 'nullable|exists:employees,id',
            'maintenance_type' => 'required|in:rutin_bakim,ariza_onarim,periyodik_kontrol,modernizasyon',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'nullable|date_format:H:i',
            'priority' => 'required|in:dusuk,normal,yuksek,acil',
            'status' => 'required|in:planli,atandi,baslandi,tamamlandi,ertelendi,iptal',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only([
            'building_id', 'maintenance_type', 'scheduled_date', 'scheduled_time',
            'priority', 'status', 'assigned_employee_id', 'description',
        ]);

        $maintenance->update($data);

        return redirect()->route('maintenance.index')->with('success', 'Bakım işi güncellendi.');
    }

    public function destroy(MaintenanceSchedule $maintenance)
    {
        // Model bulunamadıysa 404 hatası döndür
        if (!$maintenance->exists) {
            abort(404, 'Bakım kaydı bulunamadı.');
        }

        $maintenance->delete();

        return redirect()->route('maintenance.index')->with('success', 'Bakım işi silindi.');
    }

    public function showReport(MaintenanceSchedule $maintenance)
    {
        // Yetki kontrolü
        if (auth()->user()->company_id && $maintenance->building->company_id !== auth()->user()->company_id) {
            abort(403, 'Bu rapora erişim yetkiniz yok.');
        }

        $maintenance->load(['building', 'assignedEmployee', 'maintenanceReport']);

        if (!$maintenance->maintenanceReport) {
            return redirect()->route('maintenance.show', $maintenance)
                ->with('error', 'Bu iş için henüz rapor oluşturulmamış.');
        }

        return view('maintenance.report', compact('maintenance'));
    }

    public function downloadReport(MaintenanceSchedule $maintenance)
    {
        // Yetki kontrolü
        if (auth()->user()->company_id && $maintenance->building->company_id !== auth()->user()->company_id) {
            abort(403, 'Bu rapora erişim yetkiniz yok.');
        }

        if (!$maintenance->maintenanceReport) {
            return redirect()->route('maintenance.show', $maintenance)
                ->with('error', 'Bu iş için henüz rapor oluşturulmamış.');
        }

        // Maintenance verilerini yükle
        $maintenance->load(['building', 'assignedEmployee', 'maintenanceReport.employee']);

        // PDF oluştur
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.maintenance-report', compact('maintenance'));

        // PDF ayarları
        $pdf->setPaper('A4', 'portrait');

        // Dosya adı oluştur
        $fileName = 'bakim-raporu-' . $maintenance->building->name . '-' . $maintenance->scheduled_date->format('Y-m-d') . '.pdf';

        // PDF'i indir
        return $pdf->download($fileName);
    }
}
