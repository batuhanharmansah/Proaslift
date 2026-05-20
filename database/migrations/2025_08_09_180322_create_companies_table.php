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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();

            // Abonelik bilgileri
            $table->enum('subscription_plan', ['basic', 'professional', 'enterprise'])->default('basic');
            $table->enum('subscription_status', ['active', 'suspended', 'cancelled', 'trial'])->default('trial');
            $table->date('subscription_start')->nullable();
            $table->date('subscription_end')->nullable();
            $table->decimal('monthly_fee', 8, 2)->default(0);

            // Limitler
            $table->integer('max_buildings')->default(5);
            $table->integer('max_employees')->default(3);

            // Durum
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
