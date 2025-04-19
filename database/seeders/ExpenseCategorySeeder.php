<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ExpenseCategory;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ExpenseCategory::insert([
            ['name' => 'Еда', 'icon' => 'food'],
            ['name' => 'Транспорт', 'icon' => 'transport'],
            ['name' => 'Жильё', 'icon' => 'home'],
            ['name' => 'Здоровье', 'icon' => 'health'],
            ['name' => 'Развлечения', 'icon' => 'entertainment'],
            ['name' => 'Другое', 'icon' => 'other']
        ]);
    }
}
