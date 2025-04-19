<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()
            ->transactions()
            ->with(['account.currency', 'incomeCategory', 'expenseCategory'])
            ->latest('date')
            ->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'income_category_id' => 'nullable|required_without:expense_category_id|exists:income_categories,id',
            'expense_category_id' => 'nullable|required_without:income_category_id|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'comment' => 'nullable|string|max:255'
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $account = Account::findOrFail($validated['account_id']);
            $this->authorize('use', $account);

            // Обновляем баланс счета
            if (isset($validated['income_category_id'])) {
                $account->balance += $validated['amount'];
            } else {
                if ($account->balance < $validated['amount']) {
                    abort(422, 'Недостаточно средств на счете');
                }
                $account->balance -= $validated['amount'];
            }
            $account->save();

            $transaction = Transaction::create($validated);
            return response()->json($transaction->load(['account.currency', 'incomeCategory', 'expenseCategory']), 201);
        });
    }

    public function summary(Request $request)
    {
        $user = $request->user();

        $transactions = $user->transactions()
            ->with(['incomeCategory', 'expenseCategory'])
            ->get();

        return response()->json([
            'total_income' => $transactions->whereNotNull('income_category_id')->sum('amount'),
            'total_expense' => $transactions->whereNotNull('expense_category_id')->sum('amount'),
            'categories' => [
                'income' => $transactions->whereNotNull('income_category_id')
                    ->groupBy('incomeCategory.name')
                    ->map->sum('amount'),
                'expense' => $transactions->whereNotNull('expense_category_id')
                    ->groupBy('expenseCategory.name')
                    ->map->sum('amount')
            ]
        ]);
    }
}
