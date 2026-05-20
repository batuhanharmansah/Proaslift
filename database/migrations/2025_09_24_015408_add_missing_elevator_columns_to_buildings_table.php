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
            // Eksik asansör kolonlarını ekle (eğer yoksa)
            if (!Schema::hasColumn('buildings', 'elevator_code')) {
                $table->string('elevator_code')->nullable()->after('contract_end_date');
            }
            if (!Schema::hasColumn('buildings', 'capacity_kg')) {
                $table->integer('capacity_kg')->nullable()->after('elevator_code');
            }
            if (!Schema::hasColumn('buildings', 'capacity_person')) {
                $table->integer('capacity_person')->nullable()->after('capacity_kg');
            }
            if (!Schema::hasColumn('buildings', 'manufacturer')) {
                $table->string('manufacturer')->nullable()->after('capacity_person');
            }
            if (!Schema::hasColumn('buildings', 'model')) {
                $table->string('model')->nullable()->after('manufacturer');
            }
            if (!Schema::hasColumn('buildings', 'serial_number')) {
                $table->string('serial_number')->nullable()->after('model');
            }
            if (!Schema::hasColumn('buildings', 'responsible_person')) {
                $table->string('responsible_person')->nullable()->after('serial_number');
            }
            if (!Schema::hasColumn('buildings', 'responsible_phone')) {
                $table->string('responsible_phone')->nullable()->after('responsible_person');
            }
            if (!Schema::hasColumn('buildings', 'responsible_email')) {
                $table->string('responsible_email')->nullable()->after('responsible_phone');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->dropColumn([
                'elevator_code', 'capacity_kg', 'capacity_person',
                'manufacturer', 'model', 'serial_number',
                'responsible_person', 'responsible_phone', 'responsible_email'
            ]);
        });
    }
};
