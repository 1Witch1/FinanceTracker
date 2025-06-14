<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Currency;

class CurrencyController extends Controller
{
    public function index()
    {
        return Currency::all();
    }
    public function show($id)
    {
        return Currency::all()->find($id);
    }
}
