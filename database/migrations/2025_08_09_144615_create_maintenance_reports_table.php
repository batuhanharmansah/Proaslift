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
        Schema::create('maintenance_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_schedule_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->datetime('start_time');
            $table->datetime('end_time')->nullable();
            $table->text('work_description');
            $table->json('used_products')->nullable(); // kullanılan ürünler ve miktarları
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->text('problems_found')->nullable();
            $table->text('recommendations')->nullable();
            $table->enum('completion_status', ['tamamlandi', 'kismi_tamamlandi', 'ertelendi']);
            $table->boolean('customer_signature')->default(false);
            $table->string('customer_name')->nullable();
            $table->text('customer_notes')->nullable();
            $table->json('photos')->nullable(); // fotoğraf yolları
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_reports');
    }
};
