<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Transaction;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Доходы
        Transaction::create([
            'account_id' => 1,
            'income_category_id' => 1,
            'amount' => 1500.00,
            'date' => now()->subDays(5),
            'comment' => 'Аванс за май'
        ]);

        // Расходы
        Transaction::create([
            'account_id' => 1,
            'expense_category_id' => 1,
            'amount' => 75.50,
            'date' => now()->subDays(3),
            'comment' => 'Продукты на неделю'
        ]);
    }
}
