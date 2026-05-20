<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('building_financial_records')) {
            return;
        }

        Schema::create('building_financial_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('building_id')->constrained()->onDelete('cascade');
            $table->decimal('contract_amount', 15, 2); // Anlaşılan toplam tutar
            $table->decimal('monthly_amount', 15, 2); // Aylık ödeme tutarı
            $table->decimal('total_received', 15, 2)->default(0); // Toplam tahsil edilen
            $table->decimal('total_remaining', 15, 2); // Kalan tutar
            $table->date('contract_start_date'); // Sözleşme başlangıç tarihi
            $table->date('contract_end_date')->nullable(); // Sözleşme bitiş tarihi
            $table->enum('payment_frequency', ['aylik', 'uc_aylik', 'alti_aylik', 'yillik'])->default('aylik');
            $table->enum('status', ['aktif', 'pasif', 'iptal'])->default('aktif');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('building_financial_records');
    }
};
