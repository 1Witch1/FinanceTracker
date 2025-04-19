<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'goal_id',
        'account_id',
        'amount',
        'type',
        'date'
    ];

    protected $casts = [
        'date' => 'datetime'
    ];

    // Связи
    public function goal()
    {
        return $this->belongsTo(Goal::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    // Автоматическое обновление цели
    protected static function boot()
    {
        parent::boot();

        static::created(function($history) {
            $history->goal->touch();
        });

        static::deleted(function($history) {
            $history->goal->touch();
        });
    }
}
