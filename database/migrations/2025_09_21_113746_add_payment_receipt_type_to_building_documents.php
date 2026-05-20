<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('building_documents', function (Blueprint $table) {
            // Aylık ödeme dekontu için yeni alanlar (sadece yoksa ekle)
            if (!Schema::hasColumn('building_documents', 'payment_month')) {
                $table->string('payment_month')->nullable()->after('document_type'); // 2025-01 formatında
            }
            if (!Schema::hasColumn('building_documents', 'payment_amount')) {
                $table->decimal('payment_amount', 10, 2)->nullable()->after('payment_month'); // Ödeme tutarı
            }
        });

        // SQLite MODIFY COLUMN desteklemediği için enum güncellemesini sadece MySQL'de uygula.
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE building_documents MODIFY COLUMN document_type ENUM('sozlesme', 'fatura', 'bakim_raporu', 'ariza_raporu', 'teknik_cizim', 'sertifika', 'izin', 'odeme_dekontu', 'diger') DEFAULT 'diger'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('building_documents', function (Blueprint $table) {
            $table->dropColumn(['payment_month', 'payment_amount']);
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE building_documents MODIFY COLUMN document_type ENUM('sozlesme', 'fatura', 'bakim_raporu', 'ariza_raporu', 'teknik_cizim', 'sertifika', 'izin', 'diger') DEFAULT 'diger'");
        }
    }
};
