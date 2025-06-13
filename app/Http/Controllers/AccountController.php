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
            'balance' => 'required|numeric|min:0'
        ]);

        $account = $request->user()->accounts()->create([
            'name' => $validated['name'],
            'currency_id' => $validated['currency_id'],
            'balance' => $validated['balance']
        ]);

        return response()->json($account->load('currency'), 201);
    }

    public function show(Request $request, Account $account)
    {
        // Проверка, что текущий пользователь является владельцем счета
        if ($account->user_id !== auth()->id()) {
            abort(403, 'Access denied');
        }

        return response()->json($account);
    }

    public function destroy(Request $request, Account $account)
    {
        // Проверка, что текущий пользователь является владельцем счета
        if ($account->user_id !== auth()->id()) {
            abort(403, 'Access denied');
        }

        $account->delete();

        return response()->json(['message' => 'Account deleted successfully']);
    }
    public function update(Request $request, Account $account)
    {
        // Проверка, что текущий пользователь является владельцем счета
        if ($account->user_id !== auth()->id()) {
            abort(403, 'Access denied');
        }
        // Валидация данных
        $validatedData = $request->validate([
            'name' => 'required|string',
            'balance' => 'required|numeric',
            'currency_id' => 'required|exists:currencies,id',
        ]);

        // Обновление счета
        $account->update($validatedData);

        return response()->json($account);
    }
}
