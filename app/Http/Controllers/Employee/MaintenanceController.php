<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceReport;
use App\Models\Product;
use App\Models\AccountingEntry;
use App\Services\MaintenanceApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
    /**
     * Employee ID'yi güvenli şekilde al
     */
    private function getEmployeeId()
    {
        $user = auth()->user();

        if (!$user->company) {
            abort(403, 'Firma bilgileriniz bulunamadı.');
        }

        $employee = $user->company->employees()->where('email', $user->email)->first();

        if (!$employee) {
            abort(403, 'Personel kaydınız bulunamadı. Lütfen yöneticinizle iletişime geçin.');
        }

        return $employee->id;
    }

    public function index()
    {
        $employeeId = $this->getEmployeeId();

        $maintenances = MaintenanceSchedule::with(['building', 'maintenanceReport'])
            ->where('assigned_employee_id', $employeeId)
            ->orderBy('scheduled_date', 'desc')
            ->paginate(15);

        return view('employee.maintenance.index', compact('maintenances'));
    }

    public function show(MaintenanceSchedule $maintenance)
    {
        $employeeId = $this->getEmployeeId();

        // Sadece kendine atanmış işleri görebilir (loose comparison kullan - type farkı olabilir)
        if ($maintenance->assigned_employee_id != $employeeId) {
            abort(403, 'Bu işe erişim yetkiniz bulunmamaktadır.');
        }

        $maintenance->load(['building.contacts', 'assignedEmployee', 'maintenanceReport']);

        return view('employee.maintenance.show', compact('maintenance'));
    }

    public function startWork(MaintenanceSchedule $maintenance)
    {
        $employeeId = $this->getEmployeeId();

        if ($maintenance->assigned_employee_id != $employeeId) {
            abort(403);
        }

        $maintenance->update(['status' => 'baslandi']);

        return redirect()->back()->with('success', 'İş başlatıldı olarak işaretlendi.');
    }

    public function createReport(MaintenanceSchedule $maintenance)
    {
        $employeeId = $this->getEmployeeId();

        if ($maintenance->assigned_employee_id != $employeeId) {
            abort(403);
        }

        // Zaten rapor varsa düzenleme sayfasına yönlendir
        if ($maintenance->maintenanceReport) {
            return redirect()->route('employee.maintenance.edit-report', $maintenance);
        }

        $products = Product::where('company_id', auth()->user()->company_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Rutin bakım için özel form göster
        if ($maintenance->maintenance_type === 'rutin_bakim') {
            return view('employee.maintenance.create-routine-report', compact('maintenance', 'products'));
        }

        return view('employee.maintenance.create-report', compact('maintenance', 'products'));
    }

    public function storeReport(Request $request, MaintenanceSchedule $maintenance)
    {
        $employeeId = $this->getEmployeeId();

        if ($maintenance->assigned_employee_id != $employeeId) {
            abort(403);
        }

        // Rutin bakım için farklı validasyon kuralları
        if ($maintenance->maintenance_type === 'rutin_bakim') {
            $validated = $request->validate([
                'start_time' => 'required|date',
                'end_time' => 'nullable|date|after:start_time',
                'recommendations' => 'nullable|string',
                'completion_status' => 'required|in:tamamlandi,kismi_tamamlandi,ertelendi',
                'customer_name' => 'nullable|string|max:255',
                'customer_notes' => 'nullable|string',
                'routine_maintenance_checklist' => 'required|string',
                'completion_percentage' => 'required|integer|min:0|max:100',
            ]);
        } else {
            // Boş ürün satırlarını filtrele: form her zaman en az bir satır gönderir; ürün seçilmezse validasyon düşmesin
            $usedProductsInput = $request->input('used_products', []);
            if (is_array($usedProductsInput)) {
                $usedProductsInput = array_values(array_filter($usedProductsInput, function ($row) {
                    return !empty($row['product_id']) && isset($row['quantity']) && (float) $row['quantity'] > 0;
                }));
                $request->merge(['used_products' => $usedProductsInput]);
            }

            $validated = $request->validate([
                'start_time' => 'required|date',
                'end_time' => 'nullable|date|after:start_time',
                'work_description' => 'required|string|min:10',
                'problems_found' => 'nullable|string',
                'recommendations' => 'nullable|string',
                'completion_status' => 'required|in:tamamlandi,kismi_tamamlandi,ertelendi',
                'customer_signature' => 'boolean',
                'customer_name' => 'nullable|string|max:255',
                'customer_notes' => 'nullable|string',
                'used_products' => 'nullable|array',
                'used_products.*.product_id' => 'required_with:used_products|exists:products,id',
                'used_products.*.quantity' => 'required_with:used_products|numeric|min:0.1',
                'used_products.*.unit_price' => 'required_with:used_products|numeric|min:0',
            ]);
        }

        $report = DB::transaction(function () use ($validated, $maintenance, $employeeId) {
            $totalCost = 0;
            $usedProductsData = [];

            // Rutin bakım için farklı işlem
            if ($maintenance->maintenance_type === 'rutin_bakim') {
                // İş gücü maliyeti ekle (saatlik ücret: ₺150)
                $startTime = \Carbon\Carbon::parse($validated['start_time'])->setTimezone('Europe/Istanbul');
                $endTime = $validated['end_time'] ? \Carbon\Carbon::parse($validated['end_time'])->setTimezone('Europe/Istanbul') : $startTime->copy()->addHours(2);
                $workHours = $startTime->diffInMinutes($endTime) / 60;
                $laborCost = $workHours * 150; // ₺150/saat
                $totalCost += $laborCost;

                // Checklist verilerini işle
                $checklistData = json_decode($validated['routine_maintenance_checklist'], true);

                // Tamamlanmamış veya sorunlu maddeleri problems_found olarak kaydet
                $problems = [];
                foreach ($checklistData as $sectionId => $items) {
                    foreach ($items as $item) {
                        if (isset($item['has_error']) && $item['has_error']) {
                            $problems[] = "❌ HATA: " . $item['title'] . (!empty($item['notes']) ? " - " . $item['notes'] : "");
                        } elseif (!$item['checked']) {
                            $problems[] = "⚠️ Tamamlanmadı: " . $item['title'];
                        } elseif (!empty($item['notes'])) {
                            $problems[] = "ℹ️ " . $item['title'] . " - Not: " . $item['notes'];
                        }
                    }
                }

                $report = MaintenanceReport::create([
                    'company_id' => $maintenance->company_id,
                    'building_id' => $maintenance->building_id,
                    'maintenance_schedule_id' => $maintenance->id,
                    'employee_id' => $employeeId,
                    'title' => 'Rutin Bakım Raporu - ' . $maintenance->building->name,
                    'start_time' => $validated['start_time'],
                    'end_time' => $validated['end_time'],
                    'work_description' => 'Rutin Bakım Kontrolü Tamamlandı',
                    'used_products' => null,
                    'total_cost' => $totalCost,
                    'problems_found' => !empty($problems) ? implode("\n", $problems) : null,
                    'recommendations' => $validated['recommendations'],
                    'completion_status' => $validated['completion_status'],
                    'customer_signature' => false,
                    'customer_name' => $validated['customer_name'],
                    'customer_notes' => $validated['customer_notes'],
                    'photos' => null,
                    'routine_maintenance_checklist' => $checklistData,
                    'completion_percentage' => $validated['completion_percentage'],
                ]);
            } else {
                // Normal bakım işlemi
                if (!empty($validated['used_products'])) {
                    foreach ($validated['used_products'] as $productData) {
                        if ($productData['product_id'] && $productData['quantity'] > 0) {
                            $product = Product::find($productData['product_id']);

                            // Stok kontrolü
                            if ($product->stock_quantity < $productData['quantity']) {
                                return back()->withErrors(['used_products' => "{$product->name} ürünü için yeterli stok bulunmuyor. Mevcut stok: {$product->stock_quantity} {$product->unit}"]);
                            }

                            $subtotal = $productData['quantity'] * $productData['unit_price'];
                            $totalCost += $subtotal;

                            $usedProductsData[] = [
                                'product_id' => $product->id,
                                'product_name' => $product->name,
                                'product_code' => $product->code,
                                'product_category' => $product->category,
                                'quantity' => $productData['quantity'],
                                'unit' => $product->unit,
                                'unit_price' => $productData['unit_price'],
                                'subtotal' => $subtotal
                            ];

                            // Stoktan düş
                            $product->decrement('stock_quantity', $productData['quantity']);
                        }
                    }
                }

                // İş gücü maliyeti ekle (saatlik ücret: ₺150)
                $startTime = \Carbon\Carbon::parse($validated['start_time'])->setTimezone('Europe/Istanbul');
                $endTime = $validated['end_time'] ? \Carbon\Carbon::parse($validated['end_time'])->setTimezone('Europe/Istanbul') : $startTime->copy()->addHours(2);
                $workHours = $startTime->diffInMinutes($endTime) / 60;
                $laborCost = $workHours * 150; // ₺150/saat
                $totalCost += $laborCost;

                $report = MaintenanceReport::create([
                    'company_id' => $maintenance->company_id,
                    'building_id' => $maintenance->building_id,
                    'maintenance_schedule_id' => $maintenance->id,
                    'employee_id' => $employeeId,
                    'title' => $maintenance->maintenance_type_label . ' Raporu - ' . $maintenance->building->name,
                    'start_time' => $validated['start_time'],
                    'end_time' => $validated['end_time'],
                    'work_description' => $validated['work_description'],
                    'used_products' => $usedProductsData,
                    'total_cost' => $totalCost,
                    'problems_found' => $validated['problems_found'],
                    'recommendations' => $validated['recommendations'],
                    'completion_status' => $validated['completion_status'],
                    'customer_signature' => $validated['customer_signature'] ?? false,
                    'customer_name' => $validated['customer_name'],
                    'customer_notes' => $validated['customer_notes'],
                    'photos' => null,
                    'routine_maintenance_checklist' => null,
                    'completion_percentage' => null,
                ]);
            }

            // Bakım durumunu güncelle
            $maintenance->update([
                'status' => $validated['completion_status'] === 'tamamlandi' ? 'tamamlandi' : 'baslandi'
            ]);

            // Otomatik muhasebe kaydı oluştur
            if ($validated['completion_status'] === 'tamamlandi' && $totalCost > 0) {
                AccountingEntry::create([
                    'company_id' => auth()->user()->company_id,
                    'type' => 'gelir',
                    'category' => $maintenance->maintenance_type === 'rutin_bakim' ? 'Bakım Hizmeti' : 'Onarım Hizmeti',
                    'description' => "Bakım işi tamamlandı - {$maintenance->building->name}",
                    'amount' => $totalCost,
                    'vat_rate' => 20.00,
                    'vat_amount' => $totalCost * 0.20,
                    'total_amount' => $totalCost * 1.20,
                    'transaction_date' => today('Europe/Istanbul'),
                    'building_id' => $maintenance->building_id,
                    'employee_id' => $employeeId,
                    'maintenance_report_id' => $report->id,
                    'payment_method' => 'banka_havalesi',
                    'status' => 'tahsil_edildi',
                    'created_by' => auth()->id(),
                ]);
            }

            return $report;
        });

        $flashMessage = 'Bakım raporu başarıyla kaydedildi.';
        if ($validated['completion_status'] === 'tamamlandi' && $report) {
            $smsResult = app(MaintenanceApprovalService::class)
                ->initiateApprovalFlow($report->fresh(['building']));
            if ($smsResult['sent'] ?? false) {
                $flashMessage .= ' Bina yöneticisine onay SMS\'i gönderildi.';
            } elseif (($smsResult['skipped_reason'] ?? null) === 'no_contact') {
                $flashMessage .= ' Uyarı: Bina iletişim telefonu tanımlı olmadığı için onay SMS\'i gönderilemedi.';
            } elseif (($smsResult['skipped_reason'] ?? null) === 'invalid_phone') {
                $flashMessage .= ' Uyarı: Geçersiz telefon nedeniyle onay SMS\'i gönderilemedi.';
            } elseif (($smsResult['skipped_reason'] ?? null) === 'cooldown') {
                $flashMessage .= ' Onay bekleniyor (SMS daha önce gönderildi).';
            }
        }

        return redirect()->route('employee.maintenance.show', $maintenance)
            ->with('success', $flashMessage);
    }

    public function editReport(MaintenanceSchedule $maintenance)
    {
        $employeeId = $this->getEmployeeId();

        if ($maintenance->assigned_employee_id != $employeeId || !$maintenance->maintenanceReport) {
            abort(403);
        }

        $report = $maintenance->maintenanceReport;
        $products = Product::where('company_id', auth()->user()->company_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('employee.maintenance.edit-report', compact('maintenance', 'report', 'products'));
    }

    public function updateReport(Request $request, MaintenanceSchedule $maintenance)
    {
        $employeeId = $this->getEmployeeId();

        if ($maintenance->assigned_employee_id != $employeeId || !$maintenance->maintenanceReport) {
            abort(403);
        }

        $validated = $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
            'work_description' => 'required|string|min:10',
            'problems_found' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'completion_status' => 'required|in:tamamlandi,kismi_tamamlandi,ertelendi',
            'customer_signature' => 'boolean',
            'customer_name' => 'nullable|string|max:255',
            'customer_notes' => 'nullable|string',
            'products' => 'nullable|array',
            'products.*.product_id' => 'required_with:products|exists:products,id',
            'products.*.quantity' => 'required_with:products|numeric|min:0.1',
            'products.*.unit_price' => 'required_with:products|numeric|min:0',
        ]);

        // Kullanılan ürünleri işle
        $usedProductsData = [];
        $totalCost = 0;

        if (!empty($validated['products'])) {
            foreach ($validated['products'] as $productData) {
                if (!empty($productData['product_id']) && !empty($productData['quantity'])) {
                    $product = Product::find($productData['product_id']);
                    if ($product) {
                        // Stok kontrolü
                        if ($product->stock_quantity < $productData['quantity']) {
                            return back()->withErrors(['products' => "{$product->name} ürünü için yeterli stok bulunmuyor. Mevcut stok: {$product->stock_quantity} {$product->unit}"]);
                        }

                        $usedProductsData[] = [
                            'product_id' => $productData['product_id'],
                            'product_name' => $product->name,
                            'quantity' => $productData['quantity'],
                            'unit' => $product->unit ?? 'adet',
                            'unit_price' => $productData['unit_price'],
                            'subtotal' => $productData['quantity'] * $productData['unit_price']
                        ];
                        $totalCost += $productData['quantity'] * $productData['unit_price'];
                    }
                }
            }
        }

        // İş gücü maliyeti ekle (saatlik ücret: ₺150)
        $startTime = \Carbon\Carbon::parse($validated['start_time']);
        $endTime = $validated['end_time'] ? \Carbon\Carbon::parse($validated['end_time']) : $startTime->copy()->addHours(2);
        $workHours = $startTime->diffInMinutes($endTime) / 60;
        $laborCost = $workHours * 150; // ₺150/saat
        $totalCost += $laborCost;

        // Mevcut kullanılan ürünleri al
        $existingProducts = $maintenance->maintenanceReport->used_products ?? [];
        $existingProductIds = collect($existingProducts)->pluck('product_id')->toArray();

        // Yeni kullanılan ürünlerin ID'lerini al
        $newProductIds = collect($usedProductsData)->pluck('product_id')->toArray();

        // Sadece yeni eklenen ürünleri stoktan düş
        $newlyAddedProducts = collect($usedProductsData)->whereNotIn('product_id', $existingProductIds);

        foreach ($newlyAddedProducts as $productData) {
            $product = Product::find($productData['product_id']);
            if ($product) {
                $product->decrement('stock_quantity', $productData['quantity']);
            }
        }

        // Raporu güncelle
        $maintenance->maintenanceReport->update([
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'work_description' => $validated['work_description'],
            'used_products' => $usedProductsData,
            'total_cost' => $totalCost,
            'problems_found' => $validated['problems_found'],
            'recommendations' => $validated['recommendations'],
            'completion_status' => $validated['completion_status'],
            'customer_signature' => $validated['customer_signature'] ?? false,
            'customer_name' => $validated['customer_name'],
            'customer_notes' => $validated['customer_notes'],
        ]);

        // Bakım durumunu güncelle
        $maintenance->update([
            'status' => $validated['completion_status'] === 'tamamlandi' ? 'tamamlandi' : 'baslandi'
        ]);

        return redirect()->route('employee.maintenance.show', $maintenance)
            ->with('success', 'Bakım raporu başarıyla güncellendi.');
    }
}
