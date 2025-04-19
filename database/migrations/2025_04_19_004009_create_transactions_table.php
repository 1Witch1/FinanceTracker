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
            $table->foreignId('account_id')->constrained('accounts');
            $table->foreignId('income_category_id')->nullable()->constrained('income_categories');
            $table->foreignId('expense_category_id')->nullable()->constrained('expense_categories');
            $table->decimal('amount', 15, 2);
            $table->dateTime('date');
            $table->string('comment', 255)->nullable();
            $table->timestamps();

            $table->check('(
                (income_category_id IS NOT NULL AND expense_category_id IS NULL) OR
                (income_category_id IS NULL AND expense_category_id IS NOT NULL)
            )');
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
