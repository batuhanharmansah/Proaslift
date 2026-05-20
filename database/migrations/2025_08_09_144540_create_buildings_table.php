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
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address');
            $table->string('district');
            $table->string('city');
            $table->integer('floor_count');
            $table->integer('elevator_count');
            $table->enum('elevator_type', ['yolcu', 'yuk', 'hasta', 'karma']);
            $table->string('elevator_brand')->nullable();
            $table->string('elevator_model')->nullable();
            $table->year('installation_year')->nullable();
            $table->enum('contract_type', ['bakim', 'onarim', 'modernizasyon']);
            $table->decimal('monthly_fee', 10, 2);
            $table->date('contract_start_date');
            $table->date('contract_end_date');
            $table->enum('status', ['aktif', 'pasif', 'beklemede'])->default('aktif');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
