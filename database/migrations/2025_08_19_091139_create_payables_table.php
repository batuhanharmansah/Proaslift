<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('title'); // Başlık: "Elektrik faturası", "Personel maaşı"
            $table->text('description')->nullable(); // Açıklama
            $table->decimal('total_amount', 15, 2); // Toplam borç tutarı
            $table->decimal('paid_amount', 15, 2)->default(0); // Ödenen tutar
            $table->decimal('remaining_amount', 15, 2); // Kalan tutar
            $table->date('due_date'); // Vade tarihi
            $table->enum('status', ['beklemede', 'kismi_odendi', 'tamamlandi', 'gecikti'])->default('beklemede');
            $table->enum('category', ['elektrik', 'su', 'dogalgaz', 'internet', 'telefon', 'maas', 'vergi', 'sigorta', 'kira', 'diger'])->default('diger');
            $table->enum('priority', ['dusuk', 'orta', 'yuksek'])->default('orta');
            $table->string('invoice_number')->nullable(); // Fatura numarası
            $table->string('supplier_name')->nullable(); // Tedarikçi adı
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payables');
    }
};
