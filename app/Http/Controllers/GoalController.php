<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Goal;
use App\Models\HistoryGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoalController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()
            ->goals()
            ->with(['currency', 'history.account'])
            ->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0.01',
            'currency_id' => 'required|exists:currencies,id',
            'deadline' => 'nullable|date'
        ]);

        $goal = $request->user()->goals()->create($validated);
        return response()->json($goal->load('currency'), 201);
    }

    public function addAmount(Request $request, Goal $goal)
    {
        $this->authorize('update', $goal);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'account_id' => 'required|exists:accounts,id'
        ]);

        return DB::transaction(function () use ($goal, $validated, $request) {
            $account = Account::findOrFail($validated['account_id']);
            $this->authorize('use', $account);

            if ($account->balance < $validated['amount']) {
                abort(422, 'Недостаточно средств на счете');
            }

            // Снимаем со счета
            $account->balance -= $validated['amount'];
            $account->save();

            // Добавляем к цели
            $history = $goal->history()->create([
                'account_id' => $account->id,
                'amount' => $validated['amount'],
                'type' => 'deposit',
                'date' => now()
            ]);

            return response()->json($goal->load(['currency', 'history.account']));
        });
    }

    public function withdrawAmount(Request $request, Goal $goal)
    {
        $this->authorize('update', $goal);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'account_id' => 'required|exists:accounts,id'
        ]);

        return DB::transaction(function () use ($goal, $validated, $request) {
            if ($goal->current_amount < $validated['amount']) {
                abort(422, 'Недостаточно средств в цели');
            }

            $account = Account::findOrFail($validated['account_id']);
            $this->authorize('use', $account);

            // Возвращаем на счет
            $account->balance += $validated['amount'];
            $account->save();

            // Записываем в историю
            $history = $goal->history()->create([
                'account_id' => $account->id,
                'amount' => $validated['amount'],
                'type' => 'withdraw',
                'date' => now()
            ]);

            return response()->json($goal->load(['currency', 'history.account']));
        });
    }
}
