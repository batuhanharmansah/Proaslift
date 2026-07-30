<?php

namespace App\Services;

use App\Models\Building;
use App\Models\BuildingFinancialRecord;
use App\Models\Receivable;
use App\Models\RecurringPayment;
use Carbon\Carbon;

class BuildingFinancialService
{
    /**
     * Yeni bina için ilk finansal kayıtları oluşturur:
     * BuildingFinancialRecord + aylık RecurringPayment + ilk ay Receivable.
     * Web ve mobil bina oluşturma akışlarının ikisi tarafından da kullanılır.
     */
    public function createInitialRecords(Building $building, string $contractStartDate, string $contractEndDate, ?int $companyId = null, ?int $userId = null): void
    {
        $companyId = $companyId ?? $building->company_id;
        $userId = $userId ?? auth()->id();

        $contractMonths = max(1, Carbon::parse($contractStartDate)->diffInMonths(Carbon::parse($contractEndDate)));

        BuildingFinancialRecord::create([
            'company_id' => $companyId,
            'building_id' => $building->id,
            'contract_amount' => $building->monthly_fee * $contractMonths,
            'monthly_amount' => $building->monthly_fee,
            'total_received' => 0,
            'total_remaining' => $building->monthly_fee * $contractMonths,
            'contract_start_date' => $contractStartDate,
            'contract_end_date' => $contractEndDate,
            'payment_frequency' => 'aylik',
            'status' => 'aktif',
        ]);

        RecurringPayment::create([
            'company_id' => $companyId,
            'title' => $building->name . ' - Aylık Bakım Ücreti',
            'description' => $building->name . ' binası için aylık bakım hizmeti ücreti',
            'amount' => $building->monthly_fee,
            'type' => 'gelir',
            'frequency' => 'aylik',
            'category' => 'bina_geliri',
            'start_date' => $contractStartDate,
            'end_date' => $contractEndDate,
            'day_of_month' => 5,
            'building_id' => $building->id,
            'is_active' => true,
            'notes' => 'Bina oluşturulurken otomatik oluşturuldu',
            'created_by' => $userId,
        ]);

        $firstPaymentDate = Carbon::parse($contractStartDate)->addDays(5);
        if ($firstPaymentDate->isPast()) {
            $firstPaymentDate = now()->startOfMonth()->addDays(4);
        }

        Receivable::create([
            'company_id' => $companyId,
            'title' => $building->name . ' - ' . $firstPaymentDate->locale('tr')->translatedFormat('F Y') . ' Bakım Ücreti',
            'description' => $building->name . ' binası ' . $firstPaymentDate->locale('tr')->translatedFormat('F Y') . ' dönemi bakım hizmeti ücreti',
            'total_amount' => $building->monthly_fee,
            'received_amount' => 0,
            'due_date' => $firstPaymentDate,
            'status' => 'beklemede',
            'priority' => 'orta',
            'building_id' => $building->id,
            'notes' => 'Bina sözleşmesi oluşturulurken otomatik oluşturuldu',
            'created_by' => $userId,
        ]);
    }

    /**
     * monthly_fee değiştiğinde ilişkili RecurringPayment ve BuildingFinancialRecord'u günceller.
     */
    public function syncMonthlyFeeChange(Building $building): void
    {
        RecurringPayment::where('building_id', $building->id)
            ->where('is_active', true)
            ->update(['amount' => $building->monthly_fee]);

        $record = BuildingFinancialRecord::where('building_id', $building->id)
            ->where('status', 'aktif')
            ->latest()
            ->first();

        if ($record) {
            $contractMonths = max(1, Carbon::parse($record->contract_start_date)->diffInMonths(Carbon::parse($record->contract_end_date)));
            $record->update([
                'monthly_amount' => $building->monthly_fee,
                'contract_amount' => $building->monthly_fee * $contractMonths,
            ]);
        }
    }

    /**
     * Aktif düzenli ödemeler için, vadesi gelmiş ama henüz alacağı oluşturulmamış aylar için Receivable üretir.
     * Zamanlanmış görev (recurring-payments:process) tarafından çağrılır.
     */
    public function processDueRecurringPayments(): int
    {
        $created = 0;

        RecurringPayment::where('is_active', true)
            ->where('type', 'gelir')
            ->whereNotNull('building_id')
            ->chunkById(50, function ($payments) use (&$created) {
                foreach ($payments as $payment) {
                    if (!$payment->shouldCreatePayment()) {
                        continue;
                    }

                    $period = now('Europe/Istanbul');

                    $alreadyExists = Receivable::where('building_id', $payment->building_id)
                        ->where('company_id', $payment->company_id)
                        ->whereYear('due_date', $period->year)
                        ->whereMonth('due_date', $period->month)
                        ->where('notes', 'like', 'Düzenli ödemeden otomatik oluşturuldu%')
                        ->exists();

                    if ($alreadyExists) {
                        continue;
                    }

                    $dueDate = $period->copy()->day(min($payment->day_of_month ?: 5, $period->daysInMonth));

                    Receivable::create([
                        'company_id' => $payment->company_id,
                        'title' => $payment->title . ' - ' . $dueDate->locale('tr')->translatedFormat('F Y'),
                        'description' => $payment->description,
                        'total_amount' => $payment->amount,
                        'received_amount' => 0,
                        'due_date' => $dueDate,
                        'status' => 'beklemede',
                        'priority' => 'orta',
                        'building_id' => $payment->building_id,
                        'notes' => 'Düzenli ödemeden otomatik oluşturuldu (RecurringPayment #' . $payment->id . ')',
                        'created_by' => $payment->created_by,
                    ]);

                    $payment->updateNextPaymentDate();
                    $created++;
                }
            });

        return $created;
    }
}
