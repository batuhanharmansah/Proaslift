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
        Schema::create('elevator_escalations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->onDelete('cascade'); // Asansör
            $table->foreignId('elevator_label_id')->constrained()->onDelete('cascade'); // İlgili etiket

            // Eskalasyon bilgileri
            $table->integer('level'); // Eskalasyon seviyesi (1, 2, 3, ...)
            $table->enum('status', ['aktif', 'cozuldu', 'iptal'])->default('aktif');
            $table->text('description')->nullable(); // Açıklama

            // Tetikleme bilgileri
            $table->timestamp('triggered_at'); // Tetikleme zamanı
            $table->integer('days_overdue'); // Gecikme gün sayısı
            $table->date('original_due_date'); // Orijinal son tarih

            // Çözüm bilgileri
            $table->timestamp('resolved_at')->nullable(); // Çözülme zamanı
            $table->text('resolution_notes')->nullable(); // Çözüm notları
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');

            // Bildirim ve aksiyon bilgileri
            $table->json('notified_parties')->nullable(); // Bilgilendirilen taraflar
            $table->json('actions_taken')->nullable(); // Alınan aksiyonlar

            // Audit bilgileri
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();

            // İndeksler
            $table->index(['building_id', 'status']);
            $table->index(['elevator_label_id', 'level']);
            $table->index(['level', 'status']);
            $table->index('triggered_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elevator_escalations');
    }
};
