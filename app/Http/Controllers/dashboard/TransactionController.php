<?php

namespace App\Http\Controllers\dashboard;

use App\Models\Sale;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    public function index()
    {
      $transactions = \App\Models\Sale::with(['user', 'details.product'])->get();
        return view('content.dashboard.historyTransaction.index', compact('transactions'));
    }
}
