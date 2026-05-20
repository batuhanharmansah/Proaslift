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
        // Users tablosuna company_id ekle
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'company_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            });
        }

        // Employees tablosuna company_id ekle
        if (Schema::hasTable('employees') && !Schema::hasColumn('employees', 'company_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            });
        }

        // Buildings tablosuna company_id ekle
        if (Schema::hasTable('buildings') && !Schema::hasColumn('buildings', 'company_id')) {
            Schema::table('buildings', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            });
        }

        // Building_contacts tablosuna company_id ekle
        if (Schema::hasTable('building_contacts') && !Schema::hasColumn('building_contacts', 'company_id')) {
            Schema::table('building_contacts', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            });
        }

        // Maintenance_schedules tablosuna company_id ekle
        if (Schema::hasTable('maintenance_schedules') && !Schema::hasColumn('maintenance_schedules', 'company_id')) {
            Schema::table('maintenance_schedules', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            });
        }

        // Maintenance_reports tablosuna company_id ekle
        if (Schema::hasTable('maintenance_reports') && !Schema::hasColumn('maintenance_reports', 'company_id')) {
            Schema::table('maintenance_reports', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            });
        }

        // Products tablosuna company_id ekle
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'company_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            });
        }

        // Accounting_entries tablosuna company_id ekle
        if (Schema::hasTable('accounting_entries') && !Schema::hasColumn('accounting_entries', 'company_id')) {
            Schema::table('accounting_entries', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            });
        }

        // Stats tablosuna company_id ekle (dashboard stats için)
        if (Schema::hasTable('stats') && !Schema::hasColumn('stats', 'company_id')) {
            Schema::table('stats', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            });
        }

        // Activities tablosuna company_id ekle
        if (Schema::hasTable('activities') && !Schema::hasColumn('activities', 'company_id')) {
            Schema::table('activities', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            });
        }

        // Ads tablosuna company_id ekle (firma özel reklamlar için)
        if (Schema::hasTable('ads') && !Schema::hasColumn('ads', 'company_id')) {
            Schema::table('ads', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'company_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }

        if (Schema::hasTable('employees') && Schema::hasColumn('employees', 'company_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }

        if (Schema::hasTable('buildings') && Schema::hasColumn('buildings', 'company_id')) {
            Schema::table('buildings', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }

        if (Schema::hasTable('building_contacts') && Schema::hasColumn('building_contacts', 'company_id')) {
            Schema::table('building_contacts', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }

        if (Schema::hasTable('maintenance_schedules') && Schema::hasColumn('maintenance_schedules', 'company_id')) {
            Schema::table('maintenance_schedules', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }

        if (Schema::hasTable('maintenance_reports') && Schema::hasColumn('maintenance_reports', 'company_id')) {
            Schema::table('maintenance_reports', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }

        if (Schema::hasTable('products') && Schema::hasColumn('products', 'company_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }

        if (Schema::hasTable('accounting_entries') && Schema::hasColumn('accounting_entries', 'company_id')) {
            Schema::table('accounting_entries', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }

        if (Schema::hasTable('stats') && Schema::hasColumn('stats', 'company_id')) {
            Schema::table('stats', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }

        if (Schema::hasTable('activities') && Schema::hasColumn('activities', 'company_id')) {
            Schema::table('activities', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }

        if (Schema::hasTable('ads') && Schema::hasColumn('ads', 'company_id')) {
            Schema::table('ads', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }
    }
};
