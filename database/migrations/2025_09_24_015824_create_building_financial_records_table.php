<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('building_financial_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('building_id')->constrained()->onDelete('cascade');
            $table->decimal('contract_amount', 12, 2);
            $table->decimal('monthly_amount', 10, 2);
            $table->decimal('total_received', 12, 2)->default(0);
            $table->decimal('total_remaining', 12, 2);
            $table->date('contract_start_date');
            $table->date('contract_end_date');
            $table->enum('payment_frequency', ['gunluk', 'haftalik', 'aylik', 'uc_aylik', 'alti_aylik', 'yillik'])->default('aylik');
            $table->enum('status', ['aktif', 'pasif', 'tamamlandi'])->default('aktif');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'building_id']);
            $table->index(['status', 'contract_end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('building_financial_records');
    }
};
