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
        Schema::table('buildings', function (Blueprint $table) {
            // Add indexes for search performance
            $table->index(['company_id', 'name'], 'buildings_company_name_index');
            $table->index(['company_id', 'district'], 'buildings_company_district_index');
            $table->index(['company_id', 'status'], 'buildings_company_status_index');

            // Composite index for common search combinations
            $table->index(['company_id', 'name', 'district', 'status'], 'buildings_search_composite_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->dropIndex('buildings_company_name_index');
            $table->dropIndex('buildings_company_district_index');
            $table->dropIndex('buildings_company_status_index');
            $table->dropIndex('buildings_search_composite_index');
        });
    }
};
