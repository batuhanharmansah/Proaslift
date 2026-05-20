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
        Schema::table('buildings', function (Blueprint $table) {
            // Asansör özellikleri
            $table->string('elevator_code')->nullable()->after('name'); // Asansör kodu
            $table->integer('capacity_kg')->nullable()->after('elevator_code'); // Kapasite (kg)
            $table->integer('capacity_person')->nullable()->after('capacity_kg'); // Kapasite (kişi)
            $table->string('manufacturer')->nullable()->after('capacity_person'); // Üretici (elevator_brand zaten var)
            $table->string('model')->nullable()->after('manufacturer'); // Model (elevator_model zaten var)
            $table->string('serial_number')->nullable()->after('model'); // Seri numarası

            // Sorumlu kişi bilgileri
            $table->string('responsible_person')->nullable()->after('serial_number'); // Sorumlu kişi
            $table->string('responsible_phone')->nullable()->after('responsible_person'); // Sorumlu telefon
            $table->string('responsible_email')->nullable()->after('responsible_phone'); // Sorumlu email

            // Durum ve notlar
            $table->enum('operational_status', ['aktif', 'bakim', 'arizali', 'muhurlendi', 'devre_disi'])
                  ->default('aktif')->after('responsible_email'); // Operasyonel durum
            $table->text('elevator_notes')->nullable()->after('operational_status'); // Asansör notları
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->dropColumn([
                'elevator_code',
                'capacity_kg',
                'capacity_person',
                'manufacturer',
                'model',
                'serial_number',
                'responsible_person',
                'responsible_phone',
                'responsible_email',
                'operational_status',
                'elevator_notes'
            ]);
        });
    }
};
