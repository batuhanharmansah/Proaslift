<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotation_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['maintenance', 'modernization', 'installation', 'repair']);
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('default_validity_days')->default(15);
            $table->string('default_currency', 10)->default('TRY');
            $table->text('scope_inclusions')->nullable();
            $table->text('scope_exclusions')->nullable();
            $table->text('terms')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['company_id', 'type', 'is_active']);
        });

        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('quote_no', 60);
            $table->enum('type', ['maintenance', 'modernization', 'installation', 'repair']);
            $table->enum('status', ['draft', 'sent', 'viewed', 'accepted', 'rejected', 'expired', 'converted', 'cancelled'])->default('draft');
            $table->foreignId('building_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_contact_name')->nullable();
            $table->string('customer_phone', 50)->nullable();
            $table->string('customer_email')->nullable();
            $table->text('customer_address')->nullable();
            $table->foreignId('issue_report_id')->nullable()->constrained('issue_reports')->nullOnDelete();
            $table->foreignId('maintenance_schedule_id')->nullable()->constrained('maintenance_schedules')->nullOnDelete();
            $table->foreignId('elevator_label_id')->nullable()->constrained('elevator_labels')->nullOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('quotation_templates')->nullOnDelete();
            $table->date('valid_until')->nullable();
            $table->string('currency', 10)->default('TRY');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_total', 12, 2)->default(0);
            $table->decimal('vat_total', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->text('scope_summary')->nullable();
            $table->text('scope_inclusions')->nullable();
            $table->text('scope_exclusions')->nullable();
            $table->text('terms')->nullable();
            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->string('public_token', 96)->unique();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->foreignId('converted_receivable_id')->nullable()->constrained('receivables')->nullOnDelete();
            $table->timestamps();
            $table->unique(['company_id', 'quote_no']);
            $table->index(['company_id', 'type', 'status']);
            $table->index(['company_id', 'building_id']);
        });

        Schema::create('quotation_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('building_id')->nullable()->constrained()->nullOnDelete();
            $table->string('elevator_code', 100)->nullable();
            $table->string('location')->nullable();
            $table->string('elevator_type', 100)->nullable();
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->unsignedInteger('capacity_kg')->nullable();
            $table->unsignedInteger('capacity_person')->nullable();
            $table->string('speed', 50)->nullable();
            $table->unsignedInteger('stop_count')->nullable();
            $table->string('door_type', 100)->nullable();
            $table->string('drive_type', 100)->nullable();
            $table->string('machine_room', 50)->nullable();
            $table->string('current_label_color', 50)->nullable();
            $table->date('last_control_date')->nullable();
            $table->timestamps();
        });

        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quotation_unit_id')->nullable()->constrained('quotation_units')->nullOnDelete();
            $table->enum('category', ['service', 'material', 'labor', 'inspection', 'discount'])->default('service');
            $table->string('item_code', 100)->nullable();
            $table->text('description');
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->decimal('quantity', 10, 2)->default(1);
            $table->string('unit', 50)->default('adet');
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('discount_rate', 5, 2)->default(0);
            $table->decimal('vat_rate', 5, 2)->default(20);
            $table->decimal('line_subtotal', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('vat_amount', 12, 2)->default(0);
            $table->decimal('line_total', 12, 2)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['quotation_id', 'sort_order']);
        });

        Schema::create('quotation_acceptances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
            $table->enum('action', ['accepted', 'rejected']);
            $table->string('accepted_by_name');
            $table->string('accepted_by_phone', 50)->nullable();
            $table->string('accepted_by_email')->nullable();
            $table->string('accepted_ip', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->text('customer_note')->nullable();
            $table->text('terms_snapshot')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_acceptances');
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotation_units');
        Schema::dropIfExists('quotations');
        Schema::dropIfExists('quotation_templates');
    }
};
