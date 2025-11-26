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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->string('account_name');
            $table->string('bank_name')->default('Fio banka a.s.');
            $table->string('bank_code')->default('2010');
            $table->string('account_number');
            $table->string('iban')->unique();
            $table->enum('currency', ['CZK', 'EUR', 'USD'])->default('CZK');
            $table->boolean('is_transparent')->default(false)->comment('Transparent account');
            $table->string('api_token')->comment('Encrypted API token');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_sync_at')->nullable()->comment('Last synchronization timestamp');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
