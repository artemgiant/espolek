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
        Schema::create('donors', function (Blueprint $table) {
            $table->id();

            // Тип донора
            $table->enum('type', ['individual', 'company']);

            // Спільні поля
            $table->string('email')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('x_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('instagram_url')->nullable();

            // Фізична особа
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->date('birth_date')->nullable();
            $table->text('address')->nullable();

            // Юридична особа
            $table->string('company_name')->nullable();
            $table->string('ico')->nullable();
            $table->string('dic')->nullable();
            $table->text('legal_address')->nullable();
            $table->string('representative_name')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Індекси
            $table->index('type');
            $table->index('email');
            $table->index('ico');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donors');
    }
};
