<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('financial_transactions')) {
            return;
        }

        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->enum('transaction_type', ['gelir', 'gider', 'transfer', 'bakiye_aktarimi']);
            $table->enum('category', [
                'bakim_geliri', 'yedek_parca_gideri', 'personel_maasi', 'vergi', 'sigorta',
                'kasa_giris', 'kasa_cikis', 'banka_transfer', 'pos_geliri', 'nakit_geliri',
                'dukkan_gideri', 'genel_gider', 'bina_geliri', 'diger'
            ]);
            $table->text('description');
            $table->decimal('amount', 15, 2);
            $table->decimal('vat_rate', 5, 2)->default(0);
            $table->decimal('vat_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->date('transaction_date');
            $table->time('transaction_time')->nullable();

            // Hesap bilgileri
            $table->foreignId('from_account_id')->nullable()->constrained('account_types')->onDelete('set null');
            $table->foreignId('to_account_id')->nullable()->constrained('account_types')->onDelete('set null');

            // Bina ve çalışan bilgileri
            $table->foreignId('building_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('employee_id')->nullable()->constrained()->onDelete('set null');

            // Ödeme bilgileri
            $table->enum('payment_method', ['nakit', 'banka_havalesi', 'kredi_karti', 'cek', 'pos', 'transfer'])->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('receipt_number')->nullable();

            // Durum
            $table->enum('status', ['beklemede', 'tamamlandi', 'iptal'])->default('tamamlandi');
            $table->text('notes')->nullable();

            // Kim tarafından oluşturuldu
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
