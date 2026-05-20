<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds created_by to accounting_entries if missing (idempotent).
     */
    public function up(): void
    {
        if (Schema::hasColumn('accounting_entries', 'created_by')) {
            return;
        }

        Schema::table('accounting_entries', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('notes');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('accounting_entries', 'created_by')) {
            return;
        }

        Schema::table('accounting_entries', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
    }
};
