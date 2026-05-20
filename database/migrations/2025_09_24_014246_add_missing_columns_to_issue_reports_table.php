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
        Schema::table('issue_reports', function (Blueprint $table) {
            // Eksik kolonları ekle (eğer yoksa)
            if (!Schema::hasColumn('issue_reports', 'issue_type')) {
                $table->enum('issue_type', [
                    'elektrik_arizasi',
                    'mekanik_ariza',
                    'kapı_arizasi',
                    'ses_sistemi',
                    'acil_durum',
                    'diger'
                ])->after('reported_by');
            }
            if (!Schema::hasColumn('issue_reports', 'priority')) {
                $table->enum('priority', ['dusuk', 'orta', 'yuksek', 'acil'])->default('orta')->after('issue_type');
            }
            if (!Schema::hasColumn('issue_reports', 'description')) {
                $table->text('description')->after('priority');
            }
            if (!Schema::hasColumn('issue_reports', 'location_details')) {
                $table->text('location_details')->nullable()->after('description');
            }
            if (!Schema::hasColumn('issue_reports', 'contact_name')) {
                $table->string('contact_name')->nullable()->after('location_details');
            }
            if (!Schema::hasColumn('issue_reports', 'contact_phone')) {
                $table->string('contact_phone')->nullable()->after('contact_name');
            }
            if (!Schema::hasColumn('issue_reports', 'status')) {
                $table->enum('status', [
                    'bildirildi',
                    'inceleniyor',
                    'ekip_atandi',
                    'calisma_basladi',
                    'tamamlandi',
                    'iptal_edildi'
                ])->default('bildirildi')->after('contact_phone');
            }
            if (!Schema::hasColumn('issue_reports', 'assigned_employee_id')) {
                $table->foreignId('assigned_employee_id')->nullable()->constrained('employees')->onDelete('set null')->after('status');
            }
            if (!Schema::hasColumn('issue_reports', 'assigned_at')) {
                $table->timestamp('assigned_at')->nullable()->after('assigned_employee_id');
            }
            if (!Schema::hasColumn('issue_reports', 'estimated_completion_time')) {
                $table->timestamp('estimated_completion_time')->nullable()->after('assigned_at');
            }
            if (!Schema::hasColumn('issue_reports', 'actual_completion_time')) {
                $table->timestamp('actual_completion_time')->nullable()->after('estimated_completion_time');
            }
            if (!Schema::hasColumn('issue_reports', 'customer_notes')) {
                $table->text('customer_notes')->nullable()->after('actual_completion_time');
            }
            if (!Schema::hasColumn('issue_reports', 'photos')) {
                $table->json('photos')->nullable()->after('customer_notes');
            }
            if (!Schema::hasColumn('issue_reports', 'is_urgent')) {
                $table->boolean('is_urgent')->default(false)->after('photos');
            }
            if (!Schema::hasColumn('issue_reports', 'requires_immediate_attention')) {
                $table->boolean('requires_immediate_attention')->default(false)->after('is_urgent');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issue_reports', function (Blueprint $table) {
            $table->dropColumn([
                'issue_type', 'priority', 'description', 'location_details',
                'contact_name', 'contact_phone', 'status', 'assigned_employee_id',
                'assigned_at', 'estimated_completion_time', 'actual_completion_time',
                'customer_notes', 'photos', 'is_urgent', 'requires_immediate_attention'
            ]);
        });
    }
};
