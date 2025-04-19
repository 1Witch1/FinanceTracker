<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Goal;
use App\Models\HistoryGoal;

class GoalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $goal = Goal::create([
            'user_id' => 1,
            'currency_id' => 1,
            'name' => 'Новый ноутбук',
            'target_amount' => 2000.00,
            'current_amount' => 500.00,
            'deadline' => now()->addMonths(3)
        ]);

        HistoryGoal::create([
            'goal_id' => $goal->id,
            'account_id' => 1,
            'amount' => 500.00,
            'type' => 'deposit',
            'date' => now()->subDays(10)
        ]);
    }
}
