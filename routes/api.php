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

/* 1. Публичные роуты аутентификации */
Route::post('/register', [AuthController::class, 'register']); // Регистрация нового пользователя
Route::post('/login', [AuthController::class, 'login']); // Авторизация по email/login и паролю

/* 2. Защищенные роуты (требуют токен) */
Route::middleware('auth:sanctum')->group(function () {

    /* 2.1 Аутентификация */
    Route::post('/logout', [AuthController::class, 'logout']); // Выход и инвалидация токена
    Route::get('/user', [AuthController::class, 'user']); // Получение данных текущего пользователя

    /* 2.2 Управление счетами */
    Route::apiResource('accounts', AccountController::class); // Полный CRUD для счетов:
    // GET /accounts - список счетов
    // POST /accounts - создание счета
    // GET /accounts/{id} - просмотр счета
    // PUT/PATCH /accounts/{id} - обновление счета
    // DELETE /accounts/{id} - удаление счета

    /* 2.3 Управление целями */
    Route::apiResource('goals', GoalController::class)->except(['update']); // CRUD без обновления:
    // GET /goals - список целей
    // POST /goals - создание цели
    // GET /goals/{id} - просмотр цели
    // DELETE /goals/{id} - удаление цели

    Route::post('goals/{goal}/add', [GoalController::class, 'addAmount']); // Пополнение цели
    Route::post('goals/{goal}/withdraw', [GoalController::class, 'withdrawAmount']); // Снятие с цели
    Route::get('goals/{goal}/history', [GoalController::class, 'history']); // История операций по цели

    /* 2.4 Категории операций */
    Route::get('/categories/income', [CategoryController::class, 'incomeCategories']); // Список категорий доходов
    Route::get('/categories/expense', [CategoryController::class, 'expenseCategories']); // Список категорий расходов

    /* 2.5 Управление операциями */
    // Получить список транзакций по счёту
    Route::get('/accounts/{account}/transactions', [TransactionController::class, 'index']);

    // Создать новую транзакцию на счёте
    Route::post('/accounts/{account}/transactions', [TransactionController::class, 'store']);
    // Просмотреть одну транзакцию
    Route::get('/accounts/{account}/transactions/{transaction}', [TransactionController::class, 'show'])
        ->whereNumber('transaction');
    // Обновить транзакцию
    Route::put('/accounts/{account}/transactions/{transaction}', [TransactionController::class, 'update']);
    Route::patch('/accounts/{account}/transactions/{transaction}', [TransactionController::class, 'update']);
    // Удалить транзакцию
    Route::delete('/accounts/{account}/transactions/{transaction}', [TransactionController::class, 'destroy']);

    Route::get('/transactions/summary', [TransactionController::class, 'summary']); // Сводные данные для графиков
    Route::get('/transactions/report', [TransactionController::class, 'report']); // Генерация отчетов

    /* 2.6 Валюты */
    Route::get('/currencies', [CurrencyController::class, 'index']); // Список всех валют
    Route::get('/currencies/{id}', [CurrencyController::class, 'show']); // Просмотр конкретной валюты
});
