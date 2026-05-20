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
        Schema::table('building_documents', function (Blueprint $table) {
            $table->string('document_type')->default('diger')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('building_documents', function (Blueprint $table) {
            $table->enum('document_type', [
                'sozlesme', 'fatura', 'bakim_raporu', 'ariza_raporu',
                'teknik_cizim', 'sertifika', 'izin', 'diger'
            ])->default('diger')->change();
        });
    }
};
