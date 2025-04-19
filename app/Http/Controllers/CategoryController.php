<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use App\Models\IncomeCategory;


class CategoryController extends Controller
{
    public function incomeCategories()
    {
        return IncomeCategory::orderBy('name')->get();
    }

    public function expenseCategories()
    {
        return ExpenseCategory::orderBy('name')->get();
    }
}
