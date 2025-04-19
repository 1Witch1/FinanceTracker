<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'currency_id',
        'name',
        'target_amount',
        'current_amount',
        'deadline'
    ];

    protected $casts = [
        'deadline' => 'date'
    ];

    // Связи
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function history()
    {
        return $this->hasMany(HistoryGoal::class);
    }

    // Автоматический пересчет текущей суммы
    protected static function boot()
    {
        parent::boot();

        static::updating(function($goal) {
            $goal->current_amount = $goal->history()
                    ->where('type', 'deposit')
                    ->sum('amount') - $goal->history()
                    ->where('type', 'withdraw')
                    ->sum('amount');
        });
    }

    // Методы для работы с целями
    public function getProgressAttribute()
    {
        return ($this->current_amount / $this->target_amount) * 100;
    }

    public function isCompleted()
    {
        return $this->current_amount >= $this->target_amount;
    }
}
