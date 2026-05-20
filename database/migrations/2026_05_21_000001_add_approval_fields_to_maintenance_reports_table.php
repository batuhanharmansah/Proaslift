<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_reports', function (Blueprint $table) {
            $table->enum('approval_status', ['onay_bekliyor', 'onaylandi'])
                ->default('onaylandi')
                ->after('completion_status');
            $table->string('approved_by_name')->nullable()->after('approval_status');
            $table->timestamp('approved_at')->nullable()->after('approved_by_name');
            $table->string('approval_ip', 45)->nullable()->after('approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_reports', function (Blueprint $table) {
            $table->dropColumn(['approval_status', 'approved_by_name', 'approved_at', 'approval_ip']);
        });
    }
};
