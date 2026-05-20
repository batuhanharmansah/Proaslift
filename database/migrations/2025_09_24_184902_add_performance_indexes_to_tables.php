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
        // Maintenance Schedules tablosu için indexler
        Schema::table('maintenance_schedules', function (Blueprint $table) {
            // Company scope için
            $table->index(['company_id', 'status'], 'maintenance_company_status_index');
            $table->index(['company_id', 'scheduled_date'], 'maintenance_company_date_index');
            $table->index(['building_id', 'status'], 'maintenance_building_status_index');
            $table->index(['assigned_employee_id', 'status'], 'maintenance_employee_status_index');

            // Dashboard queries için composite index
            $table->index(['company_id', 'status', 'scheduled_date'], 'maintenance_dashboard_index');

            // Priority sorting için
            $table->index(['company_id', 'priority', 'scheduled_date'], 'maintenance_priority_index');
        });

        // Accounting Entries tablosu için indexler
        Schema::table('accounting_entries', function (Blueprint $table) {
            // Company scope için
            $table->index(['company_id', 'type'], 'accounting_company_type_index');
            $table->index(['company_id', 'transaction_date'], 'accounting_company_date_index');
            $table->index(['building_id', 'transaction_date'], 'accounting_building_date_index');

            // Financial reports için
            $table->index(['company_id', 'type', 'transaction_date'], 'accounting_reports_index');

            // Monthly calculations için
            $table->index(['company_id', 'type', 'category'], 'accounting_monthly_index');
        });

        // Elevator Labels tablosu için indexler
        Schema::table('elevator_labels', function (Blueprint $table) {
            // Company scope için
            $table->index(['company_id', 'status'], 'elevator_labels_company_status_index');
            $table->index(['company_id', 'label_color'], 'elevator_labels_company_color_index');
            $table->index(['building_id', 'status'], 'elevator_labels_building_status_index');

            // Control date queries için
            $table->index(['company_id', 'control_date'], 'elevator_labels_control_date_index');
            $table->index(['company_id', 'next_control_date'], 'elevator_labels_next_control_index');

            // Risk analysis için
            $table->index(['company_id', 'label_color', 'control_date'], 'elevator_labels_risk_index');
        });

        // Issue Reports tablosu için ek indexler
        Schema::table('issue_reports', function (Blueprint $table) {
            // Priority queries için
            $table->index(['company_id', 'priority', 'status'], 'issue_reports_priority_index');

            // Urgent issues için
            $table->index(['company_id', 'is_urgent', 'status'], 'issue_reports_urgent_index');

            // Date range queries için
            $table->index(['company_id', 'created_at'], 'issue_reports_created_index');
        });

        // Employees tablosu için indexler
        Schema::table('employees', function (Blueprint $table) {
            // Company scope için
            $table->index(['company_id', 'is_active'], 'employees_company_active_index');
            $table->index(['company_id', 'position'], 'employees_company_position_index');

            // Search queries için
            $table->index(['company_id', 'first_name', 'last_name'], 'employees_search_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Maintenance Schedules indexlerini kaldır
        Schema::table('maintenance_schedules', function (Blueprint $table) {
            $table->dropIndex('maintenance_company_status_index');
            $table->dropIndex('maintenance_company_date_index');
            $table->dropIndex('maintenance_building_status_index');
            $table->dropIndex('maintenance_employee_status_index');
            $table->dropIndex('maintenance_dashboard_index');
            $table->dropIndex('maintenance_priority_index');
        });

        // Accounting Entries indexlerini kaldır
        Schema::table('accounting_entries', function (Blueprint $table) {
            $table->dropIndex('accounting_company_type_index');
            $table->dropIndex('accounting_company_date_index');
            $table->dropIndex('accounting_building_date_index');
            $table->dropIndex('accounting_reports_index');
            $table->dropIndex('accounting_monthly_index');
        });

        // Elevator Labels indexlerini kaldır
        Schema::table('elevator_labels', function (Blueprint $table) {
            $table->dropIndex('elevator_labels_company_status_index');
            $table->dropIndex('elevator_labels_company_color_index');
            $table->dropIndex('elevator_labels_building_status_index');
            $table->dropIndex('elevator_labels_control_date_index');
            $table->dropIndex('elevator_labels_next_control_index');
            $table->dropIndex('elevator_labels_risk_index');
        });

        // Issue Reports indexlerini kaldır
        Schema::table('issue_reports', function (Blueprint $table) {
            $table->dropIndex('issue_reports_priority_index');
            $table->dropIndex('issue_reports_urgent_index');
            $table->dropIndex('issue_reports_created_index');
        });

        // Employees indexlerini kaldır
        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex('employees_company_active_index');
            $table->dropIndex('employees_company_position_index');
            $table->dropIndex('employees_search_index');
        });
    }
};
