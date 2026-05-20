<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds account_type_id to accounting_entries if missing (idempotent).
     */
    public function up(): void
    {
        if (Schema::hasColumn('accounting_entries', 'account_type_id')) {
            return;
        }

        Schema::table('accounting_entries', function (Blueprint $table) {
            $table->unsignedBigInteger('account_type_id')->nullable()->after('company_id');
            $table->foreign('account_type_id')->references('id')->on('account_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('accounting_entries', 'account_type_id')) {
            return;
        }

        Schema::table('accounting_entries', function (Blueprint $table) {
            $table->dropForeign(['account_type_id']);
            $table->dropColumn('account_type_id');
        });
    }
};
