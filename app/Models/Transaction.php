<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'account_id',
        'income_category_id',
        'expense_category_id',
        'amount',
        'date',
        'comment'
    ];

    protected $casts = [
        'date' => 'datetime'
    ];

    // Связи
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function incomeCategory()
    {
        return $this->belongsTo(IncomeCategory::class);
    }

    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    // Автоматическая валидация при сохранении
    protected static function boot()
    {
        parent::boot();

        static::saving(function($transaction) {
            // Если указана категория дохода, обнуляем категорию расхода
            if (!is_null($transaction->income_category_id)) {
                $transaction->expense_category_id = null;
            }
            // Если указана категория расхода, обнуляем категорию дохода
            elseif (!is_null($transaction->expense_category_id)) {
                $transaction->income_category_id = null;
            }
        });
    }
}
