<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {

//            'donation'   => 'Дарунок'
//   'loan'       => 'Позика'
//   'refund'     => 'Повернення оплати'
//   'membership' => 'Членський внесок'

            $table->string('category')->nullable()->after('operation_type')
                ->comment('donation, loan, refund, membership, other');

            if (Schema::hasColumn('transactions', 'income_type')) {
                $table->dropColumn('income_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('category');

            $table->enum('income_type', ['donation', 'membership', 'other'])
                ->nullable()
                ->after('operation_type');
        });
    }
};
