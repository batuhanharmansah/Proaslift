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
        Schema::create('maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_employee_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->enum('maintenance_type', ['rutin_bakim', 'ariza_onarim', 'periyodik_kontrol', 'modernizasyon']);
            $table->date('scheduled_date');
            $table->time('scheduled_time')->nullable();
            $table->enum('priority', ['dusuk', 'normal', 'yuksek', 'acil'])->default('normal');
            $table->enum('status', ['planli', 'atandi', 'baslandi', 'tamamlandi', 'ertelendi', 'iptal'])->default('planli');
            $table->text('description');
            $table->text('notes')->nullable();
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->integer('estimated_duration')->nullable(); // dakika cinsinden
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_schedules');
    }
};
