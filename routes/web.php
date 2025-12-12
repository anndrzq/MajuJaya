<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\dashboard\SaleController;
use App\Http\Controllers\dashboard\UserDataController;
use App\Http\Controllers\dashboard\DashboardController;
use App\Http\Controllers\dashboard\ProductDataController;
use App\Http\Controllers\dashboard\TransactionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Dashboard
Route::get('/', [LoginController::class, 'index'])->name('login')->middleware('guest');
Route::post('/', [LoginController::class, 'authenticate'])->name('login')->middleware('guest');
Route::get('/logout', [LogoutController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('Dashboard.index');

    Route::get('/userData', [UserDataController::class, 'index'])->name('userData.index');
    Route::post('/userData-store', [UserDataController::class, 'store'])->name('userData.store');
    Route::get('/userData-edit/{id}', [UserDataController::class, 'edit']);
    Route::delete('/userData-delete/{id}', [UserDataController::class, 'destroy']);


    Route::get('/products', [ProductDataController::class, 'index'])->name('products.index');
    Route::post('/products-store', [ProductDataController::class, 'store'])->name('products.store');
    Route::get('/products-edit/{id}', [ProductDataController::class, 'edit']);
    Route::delete('/products-delete/{id}', [ProductDataController::class, 'destroy']);

    Route::get('/sale', [SaleController::class, 'index'])->name('sale.index');
    Route::post('/sale/store', [SaleController::class, 'store'])->name('sale.store');

    Route::get('/transaction-history', [TransactionController::class, 'index'])->name('transaction.history');
});


