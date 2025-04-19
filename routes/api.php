<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CurrencyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::apiResource('accounts', AccountController::class);
    Route::apiResource('goals', GoalController::class)->except(['update']);
    Route::post('goals/{goal}/add', [GoalController::class, 'addAmount']);
    Route::post('goals/{goal}/withdraw', [GoalController::class, 'withdrawAmount']);

    Route::get('/categories/income', [CategoryController::class, 'incomeCategories']);
    Route::get('/categories/expense', [CategoryController::class, 'expenseCategories']);

    Route::apiResource('transactions', TransactionController::class);
    Route::get('/transactions/summary', [TransactionController::class, 'summary']);

    Route::get('/currencies', [CurrencyController::class, 'index']);
});
