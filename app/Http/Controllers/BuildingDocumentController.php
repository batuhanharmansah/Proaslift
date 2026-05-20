<?php

namespace App\Http\Controllers;

use App\Models\BuildingDocument;
use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BuildingDocumentController extends Controller
{
    private function resolveCompanyBuilding(int $buildingId): Building
    {
        return Building::where('company_id', auth()->user()->company_id)
            ->findOrFail($buildingId);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'building_id' => 'required|exists:buildings,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_type' => 'required|in:sozlesme,fatura,bakim_raporu,ariza_raporu,teknik_cizim,sertifika,izin,odeme_dekontu,diger',
            'file' => 'required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png,xls,xlsx,dwg',
            'payment_month' => 'nullable|string|regex:/^\d{4}-\d{2}$/',
            'payment_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $building = $this->resolveCompanyBuilding((int) $request->building_id);

        try {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('building-documents', $fileName, 'public');

            $document = BuildingDocument::create([
                'company_id' => auth()->user()->company_id,
                'building_id' => $building->id,
                'title' => $request->title,
                'description' => $request->description,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'document_type' => $request->document_type,
                'payment_month' => $request->payment_month,
                'payment_amount' => $request->payment_amount,
                'uploaded_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Belge başarıyla yüklendi.',
                'document' => $document
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Belge yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function download(BuildingDocument $document)
    {
        // Yetki kontrolü
        if ($document->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    public function destroy(BuildingDocument $document)
    {
        // Yetki kontrolü
        if ($document->company_id !== auth()->user()->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Bu işlem için yetkiniz yok.'
            ], 403);
        }

        try {
            // Dosyayı sil
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            // Veritabanından sil
            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Belge başarıyla silindi.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Belge silinirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Aylık ödeme dekontu yükleme
     */
    public function uploadPaymentReceipt(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'building_id' => 'required|exists:buildings,id',
            'payment_year' => 'required|integer|min:2020|max:2030',
            'payment_month' => 'required|integer|min:1|max:12',
            'payment_amount' => 'required|numeric|min:0',
            'file' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Building'in bu şirkete ait olduğunu kontrol et
        $building = $this->resolveCompanyBuilding((int) $request->building_id);

        try {
            $file = $request->file('file');
            $paymentMonth = sprintf('%04d-%02d', $request->payment_year, $request->payment_month);
            $monthName = \Carbon\Carbon::create($request->payment_year, $request->payment_month, 1)
                                      ->locale('tr')->translatedFormat('F');

            $fileName = "dekont_{$building->id}_{$paymentMonth}_" . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('building-documents/payment-receipts', $fileName, 'public');

            $document = BuildingDocument::create([
                'company_id' => auth()->user()->company_id,
                'building_id' => $building->id,
                'title' => "{$building->name} - {$monthName} {$request->payment_year} Ödeme Dekontu",
                'description' => $request->description ?: "Aylık bakım ücreti ödeme dekontu",
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'document_type' => 'odeme_dekontu',
                'payment_month' => $paymentMonth,
                'payment_amount' => $request->payment_amount,
                'uploaded_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ödeme dekontu başarıyla yüklendi.',
                'document' => $document
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dekont yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Aylık ödeme dekontlarını getir
     */
    public function getPaymentReceipts(Request $request, $buildingId)
    {
        $validator = Validator::make(array_merge($request->all(), ['building_id' => $buildingId]), [
            'building_id' => 'required|exists:buildings,id',
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Building'in bu şirkete ait olduğunu kontrol et
        $building = $this->resolveCompanyBuilding((int) $buildingId);

        $receipts = BuildingDocument::getReceiptsForMonth(
            $buildingId,
            $request->year,
            $request->month
        );

        return response()->json([
            'success' => true,
            'data' => $receipts,
            'has_receipts' => $receipts->count() > 0
        ]);
    }

    /**
     * Aylık ödeme durumunu kontrol et
     */
    public function checkPaymentStatus($buildingId, $year, $month)
    {
        // Building'in bu şirkete ait olduğunu kontrol et
        $building = $this->resolveCompanyBuilding((int) $buildingId);

        $hasReceipt = BuildingDocument::hasReceiptForMonth($buildingId, $year, $month);
        $receipts = BuildingDocument::getReceiptsForMonth($buildingId, $year, $month);

        return response()->json([
            'success' => true,
            'has_receipt' => $hasReceipt,
            'receipts' => $receipts,
            'building_name' => $building->name,
            'month_name' => \Carbon\Carbon::create($year, $month, 1)->locale('tr')->translatedFormat('F'),
            'year' => $year
        ]);
    }

    public function markAsPaid(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'building_id' => 'required|exists:buildings,id',
            'payment_year' => 'required|integer|min:2020|max:2030',
            'payment_month' => 'required|integer|min:1|max:12',
            'payment_amount' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $building = $this->resolveCompanyBuilding((int) $request->building_id);
        $buildingId = $building->id;
        $year = $request->payment_year;
        $month = $request->payment_month;
        $amount = $request->payment_amount;
        $paymentMonth = sprintf('%04d-%02d', $year, $month);

        // Doğru tarihi oluştur (seçilen ay ve yıl)
        $transactionDate = \Carbon\Carbon::create($year, $month, 1);

        // Manuel ödeme için BuildingDocument kaydı oluştur (dosya olmadan)
        $document = BuildingDocument::create([
            'company_id' => auth()->user()->company_id,
            'building_id' => $buildingId,
            'title' => "Manuel Ödeme - {$paymentMonth}",
            'description' => "Bu ayın ödemesi manuel olarak ödendi olarak işaretlendi.",
            'file_name' => null,
            'file_path' => null,
            'file_type' => null,
            'file_size' => null,
            'document_type' => 'odeme_dekontu',
            'payment_month' => $paymentMonth,
            'payment_amount' => $amount,
            'uploaded_by' => auth()->id(),
        ]);

        // AccountingEntry oluştur (gelir kaydı)
        \App\Models\AccountingEntry::create([
            'company_id' => auth()->user()->company_id,
            'account_type_id' => null,
            'type' => 'gelir',
            'category' => 'bina_geliri',
            'description' => "Aylık bakım ücreti - {$paymentMonth}",
            'amount' => $amount,
            'total_amount' => $amount,
            'transaction_date' => $transactionDate,
            'building_id' => $buildingId,
            'payment_method' => 'manuel',
            'status' => 'odendi',
            'notes' => 'Manuel olarak ödendi olarak işaretlendi',
            'created_by' => auth()->id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ödeme başarıyla ödendi olarak işaretlendi.',
            'document_id' => $document->id
        ]);
    }
}
