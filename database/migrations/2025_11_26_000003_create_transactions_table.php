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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained()->onDelete('cascade');

            // Bank API fields
            $table->string('transaction_id')->comment('Transaction ID from bank');
            $table->date('date')->comment('Transaction date');
            $table->decimal('amount', 15, 2)->comment('Transaction amount');
            $table->string('currency');
            $table->string('counter_account')->nullable()->comment('Counter account number');
            $table->string('counter_bank_code')->nullable();
            $table->string('counter_bank_name')->nullable();
            $table->string('counter_bic')->nullable();
            $table->string('counter_account_name')->nullable();
            $table->string('ks')->nullable()->comment('Constant symbol');
            $table->string('vs')->nullable()->comment('Variable symbol - IMPORTANT');
            $table->string('ss')->nullable()->comment('Specific symbol');
            $table->string('user_identification')->nullable();
            $table->text('recipient_message')->nullable();
            $table->string('transaction_type')->nullable()->comment('Transaction type from API');
            $table->string('executor')->nullable();
            $table->text('comment')->nullable();
            $table->string('instruction_id')->nullable();
            $table->string('payer_reference')->nullable();

            // Our additional fields
            $table->enum('operation_type', ['income', 'expense'])->nullable();
            $table->enum('expense_type', ['taxable', 'non_taxable'])->nullable();
            $table->enum('income_type', ['donation', 'membership', 'other'])->nullable();
            $table->text('description')->nullable();
            $table->string('document_number')->nullable()->comment('Document number for expenses');
            $table->string('confirmation_number')->nullable()->comment('Tax confirmation number');

            // Future relationships (nullable for now)
            $table->unsignedBigInteger('donor_id')->nullable()->comment('For future donor relationship');
            $table->unsignedBigInteger('supplier_id')->nullable()->comment('For future supplier relationship');
            $table->unsignedBigInteger('campaign_id')->nullable()->comment('For future campaign relationship');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->unique(['bank_account_id', 'transaction_id']);
            $table->index('date');
            $table->index('vs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
