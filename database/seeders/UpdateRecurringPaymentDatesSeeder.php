<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RecurringPayment;

class UpdateRecurringPaymentDatesSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $recurringPayments = RecurringPayment::all();

        foreach ($recurringPayments as $payment) {
            $payment->calculateNextPaymentDate();
            $payment->save();
        }

        $this->command->info('Next payment dates updated for ' . $recurringPayments->count() . ' records!');
    }
}
