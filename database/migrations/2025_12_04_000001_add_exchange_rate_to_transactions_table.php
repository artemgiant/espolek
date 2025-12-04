<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('exchange_rate', 10, 4)
                ->nullable()
                ->after('currency')
                ->comment('CNB exchange rate to CZK');

            $table->decimal('amount_czk', 15, 2)
                ->nullable()
                ->after('exchange_rate')
                ->comment('Amount in CZK');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['exchange_rate', 'amount_czk']);
        });
    }
};
