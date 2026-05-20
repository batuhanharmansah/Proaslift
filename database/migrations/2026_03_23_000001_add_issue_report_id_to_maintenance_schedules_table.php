<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('issue_report_id')->nullable()->after('company_id');
            $table->foreign('issue_report_id')->references('id')->on('issue_reports')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_schedules', function (Blueprint $table) {
            $table->dropForeign(['issue_report_id']);
            $table->dropColumn('issue_report_id');
        });
    }
};
