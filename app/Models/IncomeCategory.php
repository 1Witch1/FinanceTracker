<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomeCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'icon'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function($category) {
            abort_if($category->is_protected, 403, 'Нельзя удалять системные категории');
        });
    }

    // Связи
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'income_category_id');
    }
}
