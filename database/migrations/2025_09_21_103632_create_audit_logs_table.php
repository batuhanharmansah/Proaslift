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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Nesne bilgileri
            $table->string('object_type'); // Model adı (ElevatorLabel, Building, vs)
            $table->unsignedBigInteger('object_id'); // Model ID'si

            // Eylem bilgileri
            $table->string('action'); // created, updated, deleted, status_changed, vs
            $table->text('description')->nullable(); // İnsan okunabilir açıklama

            // Kullanıcı ve sistem bilgileri
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Kullanıcı
            $table->string('user_name')->nullable(); // Kullanıcı adı (yedek)
            $table->string('service_name')->nullable(); // Sistem servisi (cron, webhook, vs)
            $table->ipAddress('ip_address')->nullable(); // IP adresi
            $table->string('user_agent')->nullable(); // Tarayıcı bilgisi

            // Veri değişiklikleri
            $table->json('old_values')->nullable(); // Eski değerler
            $table->json('new_values')->nullable(); // Yeni değerler
            $table->json('metadata')->nullable(); // Ek meta veriler

            // Zaman bilgisi
            $table->timestamp('created_at');

            // İndeksler
            $table->index(['object_type', 'object_id']);
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
