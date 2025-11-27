<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Модифікація таблиці transactions для FioBanka API.
 * 
 * Додаємо:
 * - specification (column18 - Upřesnění, напр. "1225.00 EUR")
 * 
 * Видаляємо:
 * - payer_reference (не використовується в API)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Додаємо поле specification після instruction_id
            $table->string('specification')
                ->nullable()
                ->after('instruction_id')
                ->comment('Upřesnění - e.g. original amount in foreign currency');
            
            // Видаляємо payer_reference якщо існує
            if (Schema::hasColumn('transactions', 'payer_reference')) {
                $table->dropColumn('payer_reference');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Видаляємо specification
            $table->dropColumn('specification');
            
            // Відновлюємо payer_reference
            $table->string('payer_reference')
                ->nullable()
                ->after('instruction_id');
        });
    }
};
