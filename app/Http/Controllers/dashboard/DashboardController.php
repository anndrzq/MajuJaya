<?php

namespace App\Http\Controllers\dashboard;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEarnings = Sale::sum('total_price');
        $totalOrders = Sale::count();
        $totalProducts = Product::count();
        $recentTransactions = Sale::with('user')->latest()->take(5)->get();
        $lowStockProducts = Product::where('stock', '<=', 10)->get();

        return view('content.dashboard.index', compact(
            'totalEarnings',
            'totalOrders',
            'totalProducts',
            'recentTransactions',
            'lowStockProducts'
        ));
    }
}
