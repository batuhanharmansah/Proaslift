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
            // Check if building_id doesn't exist before adding
            if (!Schema::hasColumn('maintenance_reports', 'building_id')) {
                $table->foreignId('building_id')->after('company_id')->nullable()->constrained()->onDelete('cascade');
            }
            
            // Check if title doesn't exist before adding
            if (!Schema::hasColumn('maintenance_reports', 'title')) {
                $table->string('title')->after('building_id')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_reports', function (Blueprint $table) {
            if (Schema::hasColumn('maintenance_reports', 'building_id')) {
                $table->dropForeign(['building_id']);
                $table->dropColumn('building_id');
            }
            
            if (Schema::hasColumn('maintenance_reports', 'title')) {
                $table->dropColumn('title');
            }
        });
    }
};
