<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CurrencySeeder::class,       // Первый - не зависит ни от чего
            IncomeCategorySeeder::class, // Второй - не зависит от других таблиц
            ExpenseCategorySeeder::class,// Третий - не зависит от других таблиц
            UserSeeder::class,           // Четвертый - требует currencies
            AccountSeeder::class,        // Пятый - требует users и currencies
            GoalSeeder::class,           // Шестой - требует users и currencies
            TransactionSeeder::class     // Последний - требует все остальные
        ]);
    }
}
