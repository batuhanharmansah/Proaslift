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
        Schema::table('maintenance_reports', function (Blueprint $table) {
            $table->decimal('total_cost', 10, 2)->default(0)->after('used_products');
            $table->text('problems_found')->nullable()->after('total_cost');
            $table->enum('completion_status', ['tamamlandi', 'kismi_tamamlandi', 'ertelendi'])->default('tamamlandi')->after('problems_found');
            $table->boolean('customer_signature')->default(false)->after('completion_status');
            $table->string('customer_name')->nullable()->after('customer_signature');
            $table->text('customer_notes')->nullable()->after('customer_name');
            $table->json('photos')->nullable()->after('customer_notes');
            $table->json('routine_maintenance_checklist')->nullable()->after('photos');
            $table->integer('completion_percentage')->default(100)->after('routine_maintenance_checklist');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_reports', function (Blueprint $table) {
            $table->dropColumn([
                'total_cost',
                'problems_found',
                'completion_status',
                'customer_signature',
                'customer_name',
                'customer_notes',
                'photos',
                'routine_maintenance_checklist',
                'completion_percentage'
            ]);
        });
    }
};
