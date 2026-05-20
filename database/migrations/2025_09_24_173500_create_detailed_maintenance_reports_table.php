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
        Schema::create('detailed_maintenance_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_schedule_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('building_id')->constrained()->onDelete('cascade');

            // Form Bilgileri
            $table->string('form_number')->unique(); // Form No: 01875
            $table->string('building_id_number')->nullable(); // ID No: 3297687372
            $table->date('maintenance_date'); // Tarih: 15.05.2023
            $table->time('entry_time')->nullable(); // Giriş Saati
            $table->time('exit_time')->nullable(); // Çıkış Saati

            // Asansör Bilgileri
            $table->integer('capacity_person')->nullable(); // 10 kişilik
            $table->integer('capacity_kg')->nullable(); // 200 kg
            $table->integer('floor_count')->nullable(); // 7 duraklı
            $table->string('control_type')->nullable(); // Kumanda: …

            // Makine Dairesi Kontrolü
            $table->json('machine_room_checks')->nullable(); // Makine dairesi kontrolleri
            $table->json('floor_checks')->nullable(); // Kat kontrolleri
            $table->json('cabin_checks')->nullable(); // Kabin kontrolleri
            $table->json('shaft_checks')->nullable(); // Kuyu içi kontrolleri

            // Bakım Bilgileri
            $table->string('maintenance_month')->nullable(); // Bakım yapılan ay: MAYIS
            $table->text('description_warnings')->nullable(); // Açıklama – Uyarılar
            $table->json('faulty_parts')->nullable(); // Arızalı Parçalar
            $table->json('replaced_parts')->nullable(); // Değiştirilen Parçalar

            // İmzalar ve Onaylar
            $table->boolean('building_authority_signature')->default(false); // Bina Yetkilisi İmza
            $table->string('building_authority_name')->nullable(); // Bina Yetkilisi Adı
            $table->text('building_authority_notes')->nullable(); // "Asansör çalışır teslim aldım."
            $table->boolean('service_authority_signature')->default(false); // Servis Yetkilisi İmza
            $table->string('service_authority_name')->nullable(); // Servis Yetkilisi Adı

            // Genel Bilgiler
            $table->enum('completion_status', ['tamamlandi', 'kismi_tamamlandi', 'ertelendi'])->default('tamamlandi');
            $table->text('general_notes')->nullable(); // Genel notlar
            $table->json('photos')->nullable(); // Fotoğraflar

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detailed_maintenance_reports');
    }
};
