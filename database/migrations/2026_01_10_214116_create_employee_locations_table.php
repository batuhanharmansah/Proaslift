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
        Schema::create('employee_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('maintenance_schedule_id')->nullable()->constrained('maintenance_schedules')->onDelete('set null');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('accuracy', 8, 2)->nullable(); // GPS doğruluğu (metre)
            $table->decimal('speed', 8, 2)->nullable(); // Hız (m/s)
            $table->decimal('heading', 5, 2)->nullable(); // Yön (derece)
            $table->timestamp('recorded_at');
            $table->timestamps();

            // Indexes for performance
            $table->index(['employee_id', 'recorded_at']);
            $table->index(['maintenance_schedule_id', 'recorded_at']);
            $table->index('recorded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_locations');
    }
};
