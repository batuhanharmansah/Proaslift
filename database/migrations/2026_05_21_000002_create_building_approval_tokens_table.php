<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('building_approval_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('building_id')->constrained()->onDelete('cascade');
            $table->string('token', 64)->unique();
            $table->timestamp('expires_at');
            $table->timestamp('last_sms_sent_at')->nullable();
            $table->timestamps();

            $table->index(['building_id', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('building_approval_tokens');
    }
};
