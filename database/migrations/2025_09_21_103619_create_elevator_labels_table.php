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
        Schema::create('elevator_labels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->onDelete('cascade'); // Asansör (building olarak kullanıyoruz)

            // Etiket bilgileri
            $table->enum('label_color', ['yesil', 'mavi', 'sari', 'kirmizi']); // Etiket rengi
            $table->date('control_date'); // Kontrol tarihi
            $table->integer('follow_up_days'); // Takip süresi (gün)
            $table->date('due_date'); // Son tarih (kontrol_tarihi + takip_suresi_gun)

            // Durum ve açıklamalar
            $table->enum('status', ['aktif', 'tamamlandi', 'muhurlendi', 'iptal'])->default('aktif');
            $table->text('description')->nullable(); // Açıklama
            $table->date('correction_date')->nullable(); // Düzeltme tarihi

            // Kaynak ve meta bilgiler
            $table->enum('source', ['periyodik_kontrol', 'takip_kontrol', 'manuel'])->default('periyodik_kontrol');
            $table->boolean('is_sealing_candidate')->default(false); // Mühürleme adayı

            // Kontrol eden kişi/kurum bilgileri
            $table->string('inspector_name')->nullable(); // Kontrol eden kişi
            $table->string('inspector_company')->nullable(); // Kontrol eden firma
            $table->string('inspector_license')->nullable(); // Kontrol eden lisans no

            // Audit bilgileri
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();

            // İndeksler
            $table->index(['building_id', 'status']);
            $table->index(['due_date', 'status']);
            $table->index(['label_color', 'status']);
            $table->index('control_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elevator_labels');
    }
};
