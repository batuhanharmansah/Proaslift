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
        Schema::create('elevator_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->onDelete('cascade'); // Asansör
            $table->foreignId('elevator_label_id')->constrained()->onDelete('cascade'); // İlgili etiket

            // Bildirim tipi ve alıcı
            $table->enum('type', ['eposta', 'sms', 'push', 'webhook']);
            $table->string('recipient'); // Alıcı (email, telefon, webhook URL vs)
            $table->string('recipient_name')->nullable(); // Alıcı adı

            // İçerik
            $table->string('subject'); // Konu
            $table->text('content'); // İçerik
            $table->json('template_data')->nullable(); // Şablon verileri

            // Durum ve zaman
            $table->enum('status', ['gonderildi', 'basarisiz', 'bekliyor'])->default('bekliyor');
            $table->timestamp('sent_at')->nullable(); // Gönderim zamanı
            $table->text('error_message')->nullable(); // Hata mesajı
            $table->integer('retry_count')->default(0); // Tekrar deneme sayısı

            // Bildirim türü (uyarı, eskalasyon, vs)
            $table->enum('notification_type', ['uyari', 'eskalasyon', 'gecikme', 'muhurlenme'])->default('uyari');
            $table->integer('days_remaining')->nullable(); // Kalan gün sayısı
            $table->integer('escalation_level')->nullable(); // Eskalasyon seviyesi

            $table->timestamps();

            // İndeksler
            $table->index(['building_id', 'status']);
            $table->index(['elevator_label_id', 'status']);
            $table->index(['type', 'status']);
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elevator_notifications');
    }
};
