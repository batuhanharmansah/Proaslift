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
        Schema::table('maintenance_reports', function (Blueprint $table) {
            $table->json('routine_maintenance_checklist')->nullable()->after('photos');
            $table->integer('completion_percentage')->nullable()->after('routine_maintenance_checklist');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_reports', function (Blueprint $table) {
            $table->dropColumn(['routine_maintenance_checklist', 'completion_percentage']);
        });
    }
};
