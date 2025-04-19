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
        Schema::create('history_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goal_id')->constrained('goals');
            $table->foreignId('account_id')->constrained('accounts');
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['deposit', 'withdraw']);
            $table->dateTime('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_goals');
    }
};
