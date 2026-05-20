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
            if (!Schema::hasColumn('building_documents', 'payment_month')) {
                $table->string('payment_month', 7)->nullable()->after('document_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('building_documents', function (Blueprint $table) {
            $table->dropColumn('payment_month');
        });
    }
};
