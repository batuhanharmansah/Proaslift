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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Bildirimin gönderildiği kullanıcı
            $table->foreignId('company_id')->constrained()->onDelete('cascade'); // Şirket bazlı filtreleme için

            // Bildirim kategorisi ve tipi
            $table->enum('type', [
                'maintenance',      // Bakım
                'issue',            // Arıza
                'financial',        // Finansal
                'employee',         // Personel
                'system',           // Sistem
                'general'           // Genel
            ]);

            // Öncelik seviyesi
            $table->enum('priority', [
                'critical',         // Kritik
                'high',             // Yüksek
                'medium',           // Orta
                'low'               // Düşük
            ])->default('medium');

            // İçerik
            $table->string('title'); // Başlık
            $table->text('body');    // Mesaj içeriği

            // Deep linking için
            $table->json('data')->nullable(); // Action data (screen, params, vb.)
            $table->string('related_entity_type')->nullable(); // building, maintenance, issue, vb.
            $table->unsignedBigInteger('related_entity_id')->nullable(); // İlgili kayıt ID

            // Okundu durumu
            $table->boolean('read')->default(false);
            $table->timestamp('read_at')->nullable();

            // İndeksler
            $table->index(['user_id', 'read']);
            $table->index(['company_id', 'read']);
            $table->index(['type', 'read']);
            $table->index(['priority', 'read']);
            $table->index('created_at');
            $table->index(['related_entity_type', 'related_entity_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
