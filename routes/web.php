<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use App\Livewire\DashboardOverview;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardOverview::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('accounts', AccountController::class);
    Route::resource('transactions', TransactionController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('budgets', BudgetController::class);
    Route::resource('debts', DebtController::class);

    Route::get('/api/dashboard', [DashboardController::class, 'index'])->name('api.dashboard');
});

require __DIR__.'/auth.php';
