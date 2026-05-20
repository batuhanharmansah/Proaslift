<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * accounting_entries.type ENUM'a 'transfer' ekler.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE `accounting_entries` MODIFY COLUMN `type` ENUM('gelir', 'gider', 'maas', 'vergi', 'sigorta', 'transfer') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Dikkat: 'transfer' kayıtları varsa silinmeli veya başka türe çevrilmeli
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE `accounting_entries` MODIFY COLUMN `type` ENUM('gelir', 'gider', 'maas', 'vergi', 'sigorta') NOT NULL");
        }
    }
};
