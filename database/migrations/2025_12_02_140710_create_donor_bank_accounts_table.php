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
        Schema::create('donor_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained()->onDelete('cascade');

            $table->string('account_number');
            $table->string('bank_code')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('iban')->nullable();

            $table->timestamps();

            // Унікальність: один рахунок = один донор
            $table->unique(['account_number', 'bank_code']);
            $table->index('donor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donor_bank_accounts');
    }
};
