<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('recurring_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('title'); // Başlık: "Aylık elektrik faturası"
            $table->text('description')->nullable(); // Açıklama
            $table->decimal('amount', 15, 2); // Ödeme tutarı
            $table->enum('type', ['gelir', 'gider']); // Gelir mi gider mi
            $table->enum('frequency', ['gunluk', 'haftalik', 'aylik', 'uc_aylik', 'alti_aylik', 'yillik'])->default('aylik');
            $table->enum('category', ['elektrik', 'su', 'dogalgaz', 'internet', 'telefon', 'maas', 'vergi', 'sigorta', 'kira', 'bina_geliri', 'diger'])->default('diger');
            $table->date('start_date'); // Başlangıç tarihi
            $table->date('end_date')->nullable(); // Bitiş tarihi (null ise sürekli)
            $table->integer('day_of_month')->default(1); // Ayın kaçıncı günü
            $table->boolean('is_active')->default(true);
            $table->foreignId('building_id')->nullable()->constrained()->onDelete('set null'); // Bina geliri için
            $table->foreignId('account_id')->nullable()->constrained('account_types')->onDelete('set null'); // Hangi hesaptan
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('recurring_payments');
    }
};
