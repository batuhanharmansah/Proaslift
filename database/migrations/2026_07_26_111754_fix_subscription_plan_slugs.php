<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * subscription_plans.slug değerlerini companies.subscription_plan enum'ıyla
 * (basic/orta/super) uyumlu hale getirir. Daha önce 2025_09_28_180149 migration'ı
 * companies tablosundaki enum'ı 'professional'/'enterprise' -> 'orta'/'super' olarak
 * değiştirmişti ama subscription_plans tablosundaki seed verisi güncellenmemişti.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('subscription_plans')->where('slug', 'professional')->update(['slug' => 'orta']);
        DB::table('subscription_plans')->where('slug', 'enterprise')->update(['slug' => 'super']);
    }

    public function down(): void
    {
        DB::table('subscription_plans')->where('slug', 'orta')->update(['slug' => 'professional']);
        DB::table('subscription_plans')->where('slug', 'super')->update(['slug' => 'enterprise']);
    }
};
