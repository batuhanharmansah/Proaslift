<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\BuildingContact;
use App\Models\BuildingDocument;
use App\Models\BuildingFinancialRecord;
use App\Models\RecurringPayment;
use App\Models\Receivable;
use App\Models\ElevatorLabel;
use App\Services\GeocodingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class BuildingController extends Controller
{
    public function index(Request $request)
    {
        // Debug: Cache'i tamamen devre dışı bırak
        $companyId = auth()->user()->company_id;

        // Debug bilgisi
        \Log::info('Building Index Debug', [
            'user_id' => auth()->id(),
            'company_id' => $companyId,
            'query_string' => $request->getQueryString()
        ]);

        $query = Building::with(['primaryContact'])->where('company_id', $companyId);

        if ($request->has('search') && $request->search) {
            $search = addcslashes($request->search, '%_\\');

            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "{$search}%")
                  ->orWhere('district', 'LIKE', "{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%")
                  ->orWhere('address', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Debug: Query'yi çalıştır ve logla
        $buildings = $query->orderBy('name', 'asc')->paginate(15);

        \Log::info('Building Query Result', [
            'total_buildings' => $buildings->total(),
            'current_page_count' => $buildings->count(),
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);

        // For AJAX requests, return only the table content
        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('buildings.partials.table', compact('buildings'));
        }

        return view('buildings.index', compact('buildings'));
    }

    public function create()
    {
        return view('buildings.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'district' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'floor_count' => 'required|integer|min:1',
            'elevator_count' => 'required|integer|min:1',
            'elevator_type' => 'required|in:yolcu,yuk,hasta,karma',
            'contract_type' => 'required|in:bakim,onarim,modernizasyon',
            'monthly_fee' => 'required|numeric|min:0',
            'contract_start_date' => 'required|date',
            'contract_end_date' => 'required|date|after:contract_start_date',
            // Asansör özellikleri (isteğe bağlı)
            'elevator_code' => 'nullable|string|max:255',
            'capacity_kg' => 'nullable|integer|min:1',
            'capacity_person' => 'nullable|integer|min:1',
            'manufacturer' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'responsible_person' => 'nullable|string|max:255',
            'responsible_phone' => 'nullable|string|max:255',
            'responsible_email' => 'nullable|email|max:255',
            // Etiket bilgileri (isteğe bağlı)
            'label_color' => 'nullable|in:yesil,mavi,sari,kirmizi',
            'control_date' => 'nullable|date|before_or_equal:today',
            'label_description' => 'nullable|string',
            'inspector_name' => 'nullable|string|max:255',
            'inspector_company' => 'nullable|string|max:255',
            'inspector_license' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::transaction(function () use ($request) {
            $companyId = auth()->user()->company_id;

            // COST-OPTIMIZED: Geocoding ONLY if coordinates are missing
            // Only happens on SAVE, not on page load or listing
            if (!$request->filled('latitude') || !$request->filled('longitude')) {
                if ($request->filled('address')) {
                    $geocodingService = new GeocodingService();
                    $coordinates = $geocodingService->geocodeAddress(
                        $request->address,
                        $request->city,
                        $request->district
                    );
                    
                    if ($coordinates) {
                        $request->merge([
                            'latitude' => $coordinates['lat'],
                            'longitude' => $coordinates['lng']
                        ]);
                    }
                }
            }

            $building = Building::create(array_merge($request->all(), [
                'company_id' => $companyId,
                'operational_status' => 'aktif'
            ]));

            // Debug: Oluşturulan binayı logla
            \Log::info('Building Created', [
                'building_id' => $building->id,
                'building_name' => $building->name,
                'company_id' => $building->company_id
            ]);

            if ($request->has('contact_name') && $request->contact_name) {
                BuildingContact::create([
                    'building_id' => $building->id,
                    'name' => $request->contact_name,
                    'title' => $request->contact_title ?? 'yonetici',
                    'phone' => $request->contact_phone,
                    'email' => $request->contact_email,
                    'is_primary' => true,
                    'is_active' => true,
                ]);
            }

            // Etiket bilgisi varsa oluştur
            if ($request->filled('label_color') && $request->filled('control_date')) {
                // Takip süresini belirle
                $followUpDays = ElevatorLabel::FOLLOW_UP_DAYS[$request->label_color] ?? 365;
                $controlDate = \Carbon\Carbon::parse($request->control_date);
                $dueDate = $controlDate->copy()->addDays($followUpDays);

                \Log::info('Building Label Creation Debug', [
                    'label_color' => $request->label_color,
                    'follow_up_days' => $followUpDays,
                    'control_date' => $request->control_date,
                    'due_date' => $dueDate->format('Y-m-d'),
                ]);

                $labelData = [
                    'building_id' => $building->id,
                    'label_color' => $request->label_color,
                    'control_date' => $request->control_date,
                    'follow_up_days' => $followUpDays,
                    'due_date' => $dueDate,
                    'next_control_date' => $dueDate,
                    'description' => $request->label_description,
                    'inspector_name' => $request->inspector_name,
                    'inspector_company' => $request->inspector_company,
                    'inspector_license' => $request->inspector_license,
                    'source' => 'manuel',
                    'status' => 'aktif',
                    'created_by' => auth()->id(),
                ];

                \Log::info('Building Label Data Before Create', $labelData);

                ElevatorLabel::create($labelData);
            }

            // Finansal kayıtları oluştur
            $this->createFinancialRecords($building, $request);

            // Belgeleri yükle
            $this->uploadDocuments($request, $building);
        });

        // Cache'i temizle ki yeni bina listede görünsün
        $this->clearBuildingCache();

        return redirect()->route('buildings.index')->with('success', 'Bina başarıyla eklendi.');
    }

    private function createFinancialRecords(Building $building, Request $request)
    {
        // 1. Building Financial Record oluştur
        $contractMonths = Carbon::parse($request->contract_start_date)
            ->diffInMonths(Carbon::parse($request->contract_end_date));

        BuildingFinancialRecord::create([
            'company_id' => auth()->user()->company_id,
            'building_id' => $building->id,
            'contract_amount' => $building->monthly_fee * $contractMonths,
            'monthly_amount' => $building->monthly_fee,
            'total_received' => 0,
            'total_remaining' => $building->monthly_fee * $contractMonths,
            'contract_start_date' => $request->contract_start_date,
            'contract_end_date' => $request->contract_end_date,
            'payment_frequency' => 'aylik',
            'status' => 'aktif'
        ]);

        // 2. Düzenli ödeme kaydı oluştur (her ayın 5'inde)
        RecurringPayment::create([
            'company_id' => auth()->user()->company_id,
            'title' => $building->name . ' - Aylık Bakım Ücreti',
            'description' => $building->name . ' binası için aylık bakım hizmeti ücreti',
            'amount' => $building->monthly_fee,
            'type' => 'gelir',
            'frequency' => 'aylik',
            'category' => 'bina_geliri',
            'start_date' => $request->contract_start_date,
            'end_date' => $request->contract_end_date,
            'day_of_month' => 5, // Her ayın 5'inde
            'building_id' => $building->id,
            'is_active' => true,
            'notes' => 'Bina oluşturulurken otomatik oluşturuldu',
            'created_by' => auth()->id()
        ]);

        // 3. İlk ay için alacak kaydı oluştur
        $firstPaymentDate = Carbon::parse($request->contract_start_date)->addDays(5);
        if ($firstPaymentDate->isPast()) {
            $firstPaymentDate = now()->startOfMonth()->addDays(4); // Bu ayın 5'i
        }

        Receivable::create([
            'company_id' => auth()->user()->company_id,
            'title' => $building->name . ' - ' . $firstPaymentDate->locale('tr')->translatedFormat('F Y') . ' Bakım Ücreti',
            'description' => $building->name . ' binası ' . $firstPaymentDate->locale('tr')->translatedFormat('F Y') . ' dönemi bakım hizmeti ücreti',
            'total_amount' => $building->monthly_fee,
            'paid_amount' => 0,
            'remaining_amount' => $building->monthly_fee,
            'due_date' => $firstPaymentDate,
            'status' => 'beklemede',
            'category' => 'bina_geliri',
            'priority' => 'orta',
            'building_id' => $building->id,
            'notes' => 'Bina sözleşmesi oluşturulurken otomatik oluşturuldu',
            'created_by' => auth()->id()
        ]);
    }

    private function uploadDocuments(Request $request, Building $building)
    {
        // Sözleşme belgesi
        if ($request->hasFile('contract_document')) {
            $this->saveDocument($request->file('contract_document'), $building, 'Sözleşme Belgesi', 'sozlesme');
        }

        // Teknik çizim
        if ($request->hasFile('technical_drawing')) {
            $this->saveDocument($request->file('technical_drawing'), $building, 'Teknik Çizim', 'teknik_cizim');
        }

        // Diğer belgeler
        if ($request->hasFile('other_documents')) {
            foreach ($request->file('other_documents') as $file) {
                $this->saveDocument($file, $building, $file->getClientOriginalName(), 'diger');
            }
        }
    }

    private function saveDocument($file, Building $building, $title, $documentType)
    {
        try {
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('building-documents', $fileName, 'public');

            BuildingDocument::create([
                'company_id' => auth()->user()->company_id,
                'building_id' => $building->id,
                'title' => $title,
                'description' => 'Bina oluşturulurken yüklenen belge',
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'document_type' => $documentType,
                'uploaded_by' => auth()->id()
            ]);
        } catch (\Exception $e) {
            \Log::error('Belge yükleme hatası: ' . $e->getMessage());
        }
    }

    /**
     * Bina cache'lerini temizle
     */
    private function clearBuildingCache()
    {
        $companyId = auth()->user()->company_id;

        // Yaygın kullanılan cache anahtarlarını temizle
        $commonQueries = ['', 'search=', 'status=aktif', 'status=pasif', 'status=beklemede'];

        foreach ($commonQueries as $query) {
            $cacheKey = 'buildings_list_' . $companyId . '_' . md5($query);
            Cache::forget($cacheKey);
        }
    }

    public function show(Building $building)
    {
        $building->load(['contacts', 'maintenanceSchedules' => function($q) {
            $q->with(['assignedEmployee', 'maintenanceReport'])->latest()->take(5);
        }, 'documents' => function($q) {
            $q->latest()->take(20);
        }, 'activeLabel', 'elevatorLabels' => function($q) {
            $q->orderBy('control_date', 'desc')->take(5);
        }]);

        // Aylık ödemeleri getir - hem AccountingEntry hem de BuildingDocument'ları kontrol et
        $monthlyPayments = \App\Models\AccountingEntry::where('building_id', $building->id)
            ->where('type', 'gelir')
            ->where('category', 'bina_geliri')
            ->whereYear('transaction_date', now()->year)
            ->selectRaw('MONTH(transaction_date) as month, SUM(amount) as total_amount')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Son 12 ayın verilerini hazırla
        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $paymentMonth = sprintf('%04d-%02d', now()->year, $i);
            $hasReceipt = BuildingDocument::hasReceiptForMonth($building->id, now()->year, $i);

            // Bu ay için ödeme var mı kontrol et (AccountingEntry'den)
            $hasPayment = $monthlyPayments->has($i);
            $paymentAmount = $monthlyPayments->get($i)->total_amount ?? 0;

            // Eğer AccountingEntry'de kayıt yoksa ama BuildingDocument'da dekont varsa da ödendi say
            if (!$hasPayment && $hasReceipt) {
                $hasPayment = true;
                $paymentAmount = $building->monthly_fee; // Varsayılan olarak aylık ücret kadar
            }

            $monthlyData[$i] = [
                'month' => $i,
                'month_name' => \Carbon\Carbon::create()->month($i)->locale('tr')->translatedFormat('F'),
                'amount' => $paymentAmount,
                'expected' => $building->monthly_fee,
                'status' => $hasPayment ? 'paid' : 'unpaid',
                'has_receipt' => $hasReceipt,
                'payment_month' => $paymentMonth
            ];
        }

        // Finansal özet - güncellenmiş verilerle
        $totalPaidMonths = collect($monthlyData)->where('status', 'paid')->count();
        $totalYearlyAmount = collect($monthlyData)->where('status', 'paid')->sum('amount');

        $financialSummary = [
            'total_yearly' => $totalYearlyAmount,
            'monthly_average' => $totalPaidMonths > 0 ? $totalYearlyAmount / $totalPaidMonths : 0,
            'expected_monthly' => $building->monthly_fee,
            'expected_yearly' => $building->monthly_fee * 12,
            'months_paid' => $totalPaidMonths,
            'months_unpaid' => 12 - $totalPaidMonths,
            'collection_rate' => $totalPaidMonths > 0 ? ($totalYearlyAmount / ($building->monthly_fee * 12)) * 100 : 0
        ];

        return view('buildings.show', compact('building', 'monthlyData', 'financialSummary'));
    }

    public function edit(Building $building)
    {
        return view('buildings.edit', compact('building'));
    }

    public function update(Request $request, Building $building)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'district' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'floor_count' => 'required|integer|min:1',
            'elevator_count' => 'required|integer|min:1',
            'elevator_type' => 'required|in:yolcu,yuk,hasta,karma',
            'contract_type' => 'required|in:bakim,onarim,modernizasyon',
            'monthly_fee' => 'required|numeric|min:0',
            'contract_start_date' => 'required|date',
            'contract_end_date' => 'required|date|after:contract_start_date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // COST-OPTIMIZED: Geocoding ONLY if:
        // 1. Coordinates are missing, OR
        // 2. Address changed (clear old cache and re-geocode)
        $addressChanged = $building->address !== $request->address 
            || $building->city !== $request->city 
            || $building->district !== $request->district;

        if ($addressChanged && (!$request->filled('latitude') || !$request->filled('longitude'))) {
            // Clear old cache if address changed
            if ($addressChanged) {
                $geocodingService = new GeocodingService();
                $geocodingService->clearCache($building->address, $building->city, $building->district);
            }

            // Re-geocode with new address
            if ($request->filled('address')) {
                $geocodingService = new GeocodingService();
                $coordinates = $geocodingService->geocodeAddress(
                    $request->address,
                    $request->city,
                    $request->district
                );
                
                if ($coordinates) {
                    $request->merge([
                        'latitude' => $coordinates['lat'],
                        'longitude' => $coordinates['lng']
                    ]);
                }
            }
        }
        // If coordinates are provided manually, use them directly (no geocoding)

        $building->update($request->all());

        // Cache'i temizle ki güncellenmiş bina bilgileri görünsün
        $this->clearBuildingCache();

        return redirect()->route('buildings.index')->with('success', 'Bina bilgileri güncellendi.');
    }

    public function destroy(Building $building)
    {
        $building->delete();

        // Cache'i temizle ki silinen bina listeden kalksin
        $this->clearBuildingCache();

        return redirect()->route('buildings.index')->with('success', 'Bina silindi.');
    }
}
