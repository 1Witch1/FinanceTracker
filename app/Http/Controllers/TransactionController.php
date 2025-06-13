<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    // Вспомогательная функция проверки доступа
    private function authorizeAccount(Account $account)
    {
        if ($account->user_id !== auth()->id()) {
            abort(403, 'Access denied');
        }
    }
    public function index(Account $account)
    {
        $this->authorizeAccount($account);
        $transactions = $account->transactions()->orderByDesc('date')->get();
        return response()->json($transactions);
    }

    // POST /accounts/{account}/transactions
    public function store(Request $request, Account $account)
    {

        // Проверка владельца счёта
        if ($account->user_id !== auth()->id()) {
            abort(403, 'Access denied');
        }

        $validated = $request->validate([
            'type' => ['required', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
            'comment' => ['nullable', 'string'],
            'income_category_id' => ['required_if:type,income', 'exists:income_categories,id'],
            'expense_category_id' => ['required_if:type,expense', 'exists:expense_categories,id'],
        ]);

        // Определяем сумму со знаком
        $signedAmount = $validated['type'] === 'income'
            ? $validated['amount']
            : -$validated['amount'];

        // Создаём транзакцию
        $transaction = $account->transactions()->create([
            'amount' => $signedAmount,
            'date' => $validated['date'],
            'comment' => $validated['comment'] ?? null,
            'income_category_id' => $validated['income_category_id'] ?? null,
            'expense_category_id' => $validated['expense_category_id'] ?? null,
        ]);

        // Обновляем баланс
        $account->balance += $signedAmount;
        $account->save();

        return response()->json($transaction, 201);
    }


    // GET /accounts/{account}/transactions/{transaction}
    public function show(Account $account, Transaction $transaction)
    {
        $this->authorizeAccount($account);

        // Убедимся, что транзакция принадлежит счёту
        if ($transaction->account_id !== $account->id) {
            abort(404, 'Transaction not found on this account');
        }

        return response()->json($transaction);
    }

    // PUT/PATCH /accounts/{account}/transactions/{transaction}
    public function update(Request $request, Account $account, Transaction $transaction)
    {
        $this->authorizeAccount($account);

        // Проверяем, что транзакция принадлежит счёту
        if ($transaction->account_id !== $account->id) {
            abort(404, 'Transaction not found on this account');
        }

        $validated = $request->validate([
            'type' => ['required', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
            'comment' => ['nullable', 'string'],
            'income_category_id' => ['required_if:type,income', 'exists:income_categories,id'],
            'expense_category_id' => ['required_if:type,expense', 'exists:expense_categories,id'],
        ]);

        $oldAmount = $transaction->amount;
        $newAmount = $validated['type'] === 'income'
            ? $validated['amount']
            : -$validated['amount'];

        $transaction->update([
            'amount' => $newAmount,
            'date' => $validated['date'],
            'comment' => $validated['comment'] ?? null,
            'income_category_id' => $validated['income_category_id'] ?? null,
            'expense_category_id' => $validated['expense_category_id'] ?? null,
        ]);

        // Обновляем баланс
        $account = $transaction->account;
        $account->balance += ($newAmount - $oldAmount);
        $account->save();

        return response()->json($transaction);
    }

    // DELETE /accounts/{account}/transactions/{transaction}
    public function destroy(Account $account, Transaction $transaction)
    {
        $this->authorizeAccount($account);

        if ($transaction->account_id !== $account->id) {
            abort(404, 'Transaction not found on this account');
        }

        $account->balance -= $transaction->amount;
        $account->save();

        $transaction->delete();

        return response()->json(['message' => 'Transaction deleted successfully']);
    }

    public function summary(Request $request)
    {
        $user = $request->user();

        // Получаем все транзакции всех счетов пользователя
        $transactions = Transaction::whereHas('account', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['incomeCategory', 'expenseCategory'])->get();

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
    public function report(Request $request)
    {
        $user = $request->user();

        // Получаем параметры из запроса
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $type = $request->query('type'); // income / expense / all

        // Базовый запрос: все транзакции пользователя
        $query = Transaction::whereHas('account', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with(['incomeCategory', 'expenseCategory']);

        // Фильтр по дате
        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        // Фильтр по типу операции
        if ($type === 'income') {
            $query->whereNotNull('income_category_id');
        } elseif ($type === 'expense') {
            $query->whereNotNull('expense_category_id');
        }

        // Выполняем запрос
        $transactions = $query->orderByDesc('date')->get();

        // Подсчет итогов
        $totalIncome = $transactions->whereNotNull('income_category_id')->sum('amount');
        $totalExpense = $transactions->whereNotNull('expense_category_id')->sum('amount');

        return response()->json([
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'type' => $type ?? 'all',
            ],
            'total_income' => round($totalIncome, 2),
            'total_expense' => round($totalExpense, 2),
            'net_balance' => round($totalIncome - $totalExpense, 2),
            'transactions' => $transactions->map(function ($t) {
                return [
                    'id' => $t->id,
                    'account_id' => $t->account_id,
                    'category' => optional($t->incomeCategory ?? $t->expenseCategory)->name,
                    'type' => $t->income_category_id ? 'income' : 'expense',
                    'amount' => round($t->amount, 2),
                    'date' => $t->date,
                    'comment' => $t->comment,
                ];
            }),
        ]);
    }
}
