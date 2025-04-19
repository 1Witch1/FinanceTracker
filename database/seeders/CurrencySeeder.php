<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Currency::insert([
            ['code' => 'USD', 'name' => 'Доллар США'],
            ['code' => 'EUR', 'name' => 'Евро'],
            ['code' => 'RUB', 'name' => 'Рубль'],
            ['code' => 'KZT', 'name' => 'Тенге']
        ]);
    }
}
