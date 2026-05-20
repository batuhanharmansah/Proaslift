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
        Schema::create('location_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_schedule_id')->constrained('maintenance_schedules')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('building_id')->constrained('buildings')->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            
            // Check types: 'arrival' (geliş), 'departure' (ayrılış)
            $table->enum('check_type', ['arrival', 'departure']);
            
            // Scheduled times
            $table->datetime('scheduled_time'); // Planlanan zaman
            $table->datetime('actual_time')->nullable(); // Gerçek zaman (null ise henüz gelmedi/ayrılmadı)
            
            // Location data when checked
            $table->decimal('employee_latitude', 10, 8)->nullable();
            $table->decimal('employee_longitude', 11, 8)->nullable();
            $table->decimal('building_latitude', 10, 8);
            $table->decimal('building_longitude', 11, 8);
            
            // Distance calculation (metre)
            $table->decimal('distance_from_building', 10, 2)->nullable();
            
            // Check results
            $table->boolean('is_on_time')->default(false); // ±15 dakika içinde mi?
            $table->integer('time_difference_minutes')->nullable(); // Zaman farkı (dakika)
            $table->enum('status', ['pending', 'on_time', 'late', 'early', 'missed'])->default('pending');
            
            // Tolerance window (default 15 minutes)
            $table->integer('tolerance_minutes')->default(15);
            
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['maintenance_schedule_id', 'check_type']);
            $table->index(['employee_id', 'status']);
            $table->index('scheduled_time');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_checks');
    }
};
