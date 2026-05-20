<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('recurring_payments', function (Blueprint $table) {
            $table->date('next_payment_date')->nullable()->after('is_active');
            $table->date('last_payment_date')->nullable()->after('next_payment_date');
        });
    }

    public function down()
    {
        Schema::table('recurring_payments', function (Blueprint $table) {
            $table->dropColumn(['next_payment_date', 'last_payment_date']);
        });
    }
};
