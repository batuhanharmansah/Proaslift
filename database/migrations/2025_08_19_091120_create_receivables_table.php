<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('receivables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('building_id')->constrained()->onDelete('cascade');
            $table->string('title'); // Başlık: "Ocak ayı bakım hizmeti"
            $table->text('description')->nullable(); // Açıklama
            $table->decimal('total_amount', 15, 2); // Toplam alacak tutarı
            $table->decimal('received_amount', 15, 2)->default(0); // Tahsil edilen tutar
            $table->decimal('remaining_amount', 15, 2); // Kalan tutar
            $table->date('due_date'); // Vade tarihi
            $table->enum('status', ['beklemede', 'kismi_odendi', 'tamamlandi', 'gecikti'])->default('beklemede');
            $table->enum('payment_type', ['tek_sefer', 'taksitli'])->default('tek_sefer');
            $table->integer('installment_count')->default(1); // Taksit sayısı
            $table->integer('paid_installments')->default(0); // Ödenen taksit sayısı
            $table->decimal('installment_amount', 15, 2); // Taksit tutarı
            $table->enum('priority', ['dusuk', 'orta', 'yuksek'])->default('orta');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('receivables');
    }
};
