<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\IncomeCategory;

class IncomeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        IncomeCategory::insert([
            ['name' => 'Зарплата', 'icon' => 'salary'],
            ['name' => 'Фриланс', 'icon' => 'freelance'],
            ['name' => 'Подарок', 'icon' => 'gift'],
            ['name' => 'Инвестиции', 'icon' => 'investment'],
            ['name' => 'Другое', 'icon' => 'other']
        ]);
    }
}
