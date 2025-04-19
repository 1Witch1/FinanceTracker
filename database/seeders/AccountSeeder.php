<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Account;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Account::create([
            'user_id' => 1,
            'currency_id' => 1,
            'name' => 'Основной счет',
            'balance' => 1000.00
        ]);
    }
}
