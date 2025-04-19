<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->accounts()->with('currency')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'currency_id' => 'required|exists:currencies,id',
            'initial_balance' => 'required|numeric|min:0'
        ]);

        $account = $request->user()->accounts()->create([
            'name' => $validated['name'],
            'currency_id' => $validated['currency_id'],
            'balance' => $validated['initial_balance']
        ]);

        return response()->json($account->load('currency'), 201);
    }

    public function show(Request $request, Account $account)
    {
        $this->authorize('view', $account);
        return $account->load(['currency', 'transactions']);
    }

    public function destroy(Request $request, Account $account)
    {
        $this->authorize('delete', $account);

        DB::transaction(function () use ($account) {
            if ($account->balance > 0) {
                abort(422, 'Нельзя удалить счет с положительным балансом');
            }
            $account->delete();
        });

        return response()->noContent();
    }
}
