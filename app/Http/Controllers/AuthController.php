<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'login' => 'required|string|max:100|unique:users',
            'email' => 'required|email|max:100|unique:users',
            'password' => 'required|string|min:8',
            'currency_id' => 'required|exists:currencies,id'
        ]);

        $user = User::create([
            'login' => $validated['login'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'currency_id' => $validated['currency_id']
        ]);

        // Создаем основной счет
        $user->accounts()->create([
            'name' => 'Основной счет',
            'currency_id' => $validated['currency_id'],
            'balance' => 0
        ]);

        return response()->json([
            'user' => $user,
            'message' => 'Пользователь успешно зарегистрирован!'
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Неверные учетные данные'], 401);
        }

        $user = Auth::user();
        return response()->json([
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Успешный выход']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user()->load(['currency', 'accounts']));
    }
}
