<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'building_id',
        'title',
        'description',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'document_type',
        'status',
        'uploaded_by',
        'payment_month',
        'payment_amount'
    ];

    protected $casts = [
        'file_size' => 'integer',
        'payment_amount' => 'decimal:2',
    ];

    public function getDocumentTypeLabelAttribute()
    {
        $types = [
            'sozlesme' => 'Sözleşme',
            'fatura' => 'Fatura',
            'bakim_raporu' => 'Bakım Raporu',
            'ariza_raporu' => 'Arıza Raporu',
            'teknik_cizim' => 'Teknik Çizim',
            'sertifika' => 'Sertifika',
            'izin' => 'İzin',
            'odeme_dekontu' => 'Ödeme Dekontu',
            'diger' => 'Diğer'
        ];
        return $types[$this->document_type] ?? $this->document_type;
    }

    public function getStatusLabelAttribute()
    {
        $statuses = [
            'aktif' => 'Aktif',
            'pasif' => 'Pasif'
        ];
        return $statuses[$this->status] ?? $this->status;
    }

    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Aylık ödeme dekontu scope'ları
    public function scopePaymentReceipts($query)
    {
        return $query->where('document_type', 'odeme_dekontu');
    }

    public function scopeForMonth($query, $year, $month)
    {
        $paymentMonth = sprintf('%04d-%02d', $year, $month);
        return $query->where('payment_month', $paymentMonth);
    }

    // Aylık ödeme dekontu yardımcı metodları
    public function getPaymentMonthFormattedAttribute()
    {
        if (!$this->payment_month) {
            return null;
        }

        [$year, $month] = explode('-', $this->payment_month);
        $monthName = \Carbon\Carbon::create($year, $month, 1)->locale('tr')->translatedFormat('F');
        return "{$monthName} {$year}";
    }

    public function isPaymentReceipt()
    {
        return $this->document_type === 'odeme_dekontu';
    }

    // Belirli bir ay için dekont var mı kontrol et
    public static function hasReceiptForMonth($buildingId, $year, $month)
    {
        $paymentMonth = sprintf('%04d-%02d', $year, $month);
        return self::where('building_id', $buildingId)
                  ->where('document_type', 'odeme_dekontu')
                  ->where('payment_month', $paymentMonth)
                  ->exists();
    }

    // Belirli bir ay için dekontları getir
    public static function getReceiptsForMonth($buildingId, $year, $month)
    {
        $paymentMonth = sprintf('%04d-%02d', $year, $month);
        return self::where('building_id', $buildingId)
                  ->where('document_type', 'odeme_dekontu')
                  ->where('payment_month', $paymentMonth)
                  ->orderBy('created_at', 'desc')
                  ->get();
    }
}
