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
        Schema::table('accounting_entries', function (Blueprint $table) {
            $table->string('category')->nullable()->after('type');
            $table->decimal('vat_rate', 5, 2)->default(20.00)->after('amount');
            $table->decimal('vat_amount', 10, 2)->default(0)->after('vat_rate');
            $table->decimal('total_amount', 12, 2)->after('vat_amount');
            $table->string('invoice_number')->nullable()->after('total_amount');
            $table->foreignId('employee_id')->nullable()->constrained()->onDelete('set null')->after('building_id');
            $table->unsignedBigInteger('maintenance_report_id')->nullable()->after('employee_id');
            $table->enum('payment_method', ['nakit', 'banka_havalesi', 'kredi_karti', 'cek'])->after('maintenance_report_id');
            $table->enum('status', ['beklemede', 'odendi', 'tahsil_edildi', 'iptal'])->default('beklemede')->after('payment_method');
            $table->text('notes')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounting_entries', function (Blueprint $table) {
            $table->dropColumn([
                'category', 'vat_rate', 'vat_amount', 'total_amount',
                'invoice_number', 'employee_id', 'maintenance_report_id',
                'payment_method', 'status', 'notes'
            ]);
        });
    }
};
