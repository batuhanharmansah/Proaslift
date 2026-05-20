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
        Schema::create('issue_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('building_id')->constrained()->onDelete('cascade');
            $table->string('reported_by'); // Bildiren kişi adı
            $table->enum('issue_type', [
                'elektrik_arizasi',
                'mekanik_ariza',
                'kapı_arizasi',
                'ses_sistemi',
                'acil_durum',
                'diger'
            ]);
            $table->enum('priority', ['dusuk', 'orta', 'yuksek', 'acil'])->default('orta');
            $table->text('description'); // Arıza açıklaması
            $table->text('location_details')->nullable(); // Konum detayları
            $table->string('contact_name')->nullable(); // İletişim kişisi
            $table->string('contact_phone')->nullable(); // İletişim telefonu
            $table->enum('status', [
                'bildirildi',
                'inceleniyor',
                'ekip_atandi',
                'calisma_basladi',
                'tamamlandi',
                'iptal_edildi'
            ])->default('bildirildi');
            $table->foreignId('assigned_employee_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->timestamp('assigned_at')->nullable(); // Ekip atama tarihi
            $table->timestamp('estimated_completion_time')->nullable(); // Tahmini tamamlanma süresi
            $table->timestamp('actual_completion_time')->nullable(); // Gerçek tamamlanma süresi
            $table->text('customer_notes')->nullable(); // Müşteri notları
            $table->json('photos')->nullable(); // Fotoğraflar
            $table->boolean('is_urgent')->default(false); // Acil mi?
            $table->boolean('requires_immediate_attention')->default(false); // Anında müdahale gerekli mi?
            $table->timestamps();

            // İndeksler
            $table->index(['company_id', 'status']);
            $table->index(['building_id', 'status']);
            $table->index(['assigned_employee_id', 'status']);
            $table->index(['priority', 'status']);
            $table->index('is_urgent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_reports');
    }
};
