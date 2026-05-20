<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * TC Kimlik No alanı kaldırıldı; varsayılan şifre artık otomatik üretiliyor.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('employees', 'tc_no')) {
            return;
        }

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('tc_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('tc_no', 11)->nullable()->unique()->after('email');
        });
    }
};
