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
        Schema::table('maintenance_schedules', function (Blueprint $table) {
            // Eksik kolonları ekle (eğer yoksa)
            if (!Schema::hasColumn('maintenance_schedules', 'maintenance_type')) {
                $table->enum('maintenance_type', ['rutin_bakim', 'ariza_onarim', 'periyodik_kontrol', 'modernizasyon'])->after('assigned_employee_id');
            }
            if (!Schema::hasColumn('maintenance_schedules', 'scheduled_time')) {
                $table->time('scheduled_time')->nullable()->after('scheduled_date');
            }
            if (!Schema::hasColumn('maintenance_schedules', 'priority')) {
                $table->enum('priority', ['dusuk', 'normal', 'yuksek', 'acil'])->default('normal')->after('scheduled_time');
            }
            if (!Schema::hasColumn('maintenance_schedules', 'status')) {
                $table->enum('status', ['planli', 'atandi', 'baslandi', 'tamamlandi', 'ertelendi', 'iptal'])->default('planli')->after('priority');
            }
            if (!Schema::hasColumn('maintenance_schedules', 'description')) {
                $table->text('description')->after('status');
            }
            if (!Schema::hasColumn('maintenance_schedules', 'notes')) {
                $table->text('notes')->nullable()->after('description');
            }
            if (!Schema::hasColumn('maintenance_schedules', 'estimated_cost')) {
                $table->decimal('estimated_cost', 10, 2)->nullable()->after('notes');
            }
            if (!Schema::hasColumn('maintenance_schedules', 'estimated_duration')) {
                $table->integer('estimated_duration')->nullable()->after('estimated_cost');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_schedules', function (Blueprint $table) {
            $table->dropColumn([
                'maintenance_type', 'scheduled_time', 'priority', 'status',
                'description', 'notes', 'estimated_cost', 'estimated_duration'
            ]);
        });
    }
};
