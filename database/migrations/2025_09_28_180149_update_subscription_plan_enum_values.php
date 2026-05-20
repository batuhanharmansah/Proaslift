<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Önce mevcut değerleri güncelle
        DB::statement("UPDATE companies SET subscription_plan = 'orta' WHERE subscription_plan = 'professional'");
        DB::statement("UPDATE companies SET subscription_plan = 'super' WHERE subscription_plan = 'enterprise'");

        // Enum değerlerini güncelle
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE companies MODIFY COLUMN subscription_plan enum('basic','orta','super') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'basic'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Değerleri geri çevir
        DB::statement("UPDATE companies SET subscription_plan = 'professional' WHERE subscription_plan = 'orta'");
        DB::statement("UPDATE companies SET subscription_plan = 'enterprise' WHERE subscription_plan = 'super'");

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE companies MODIFY COLUMN subscription_plan enum('basic','professional','enterprise') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'basic'");
        }
    }
};
