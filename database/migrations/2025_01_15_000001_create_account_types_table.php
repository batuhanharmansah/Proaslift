<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('account_types')) {
            return;
        }

        Schema::create('account_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Kasa, Banka Hesabı, Nakit, POS
            $table->string('account_number')->nullable(); // Banka hesap numarası
            $table->string('bank_name')->nullable(); // Banka adı
            $table->string('branch_name')->nullable(); // Şube adı
            $table->decimal('initial_balance', 15, 2)->default(0); // Başlangıç bakiyesi
            $table->decimal('current_balance', 15, 2)->default(0); // Güncel bakiye
            $table->enum('type', ['kasa', 'banka', 'nakit', 'pos']); // Hesap türü
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_types');
    }
};
