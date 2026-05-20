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
        Schema::create('accounting_entries', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['gelir', 'gider', 'maas', 'vergi', 'sigorta']);
            $table->string('category'); // bakim_geliri, yedek_parca_gideri, personel_maasi, etc.
            $table->text('description');
            $table->decimal('amount', 12, 2);
            $table->decimal('vat_rate', 5, 2)->default(20.00); // KDV oranı %20
            $table->decimal('vat_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 12, 2); // KDV dahil toplam
            $table->date('transaction_date');
            $table->string('invoice_number')->nullable();
            $table->foreignId('building_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('employee_id')->nullable()->constrained()->onDelete('set null');
            $table->unsignedBigInteger('maintenance_report_id')->nullable();
            $table->enum('payment_method', ['nakit', 'banka_havalesi', 'kredi_karti', 'cek']);
            $table->enum('status', ['beklemede', 'odendi', 'tahsil_edildi', 'iptal'])->default('beklemede');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_entries');
    }
};
