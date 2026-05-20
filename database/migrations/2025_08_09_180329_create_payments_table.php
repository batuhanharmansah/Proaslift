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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->date('period_start'); // Ödemenin kapsadığı dönem başlangıcı
            $table->date('period_end');   // Ödemenin kapsadığı dönem sonu
            $table->enum('status', ['paid', 'pending', 'overdue', 'cancelled'])->default('pending');
            $table->enum('payment_method', ['nakit', 'banka_havalesi', 'kredi_karti', 'cek'])->default('banka_havalesi');
            $table->string('invoice_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('recorded_at')->nullable(); // Super admin tarafından kaydedilme zamanı
            $table->foreignId('recorded_by')->nullable()->constrained('users'); // Kaydeden super admin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
