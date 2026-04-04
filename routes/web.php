<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Livewire\CategoryManager;
use App\Livewire\CsvImporter;
use App\Livewire\DashboardOverview;
use App\Livewire\PdfImporter;
use Illuminate\Support\Facades\Route;
use App\Livewire\AccountManager;
use App\Livewire\TransactionList;
use App\Livewire\BudgetManager;
use App\Livewire\DebtManager;
use App\Livewire\UserSettings;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardOverview::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/accounts', AccountManager::class)->name('accounts.index');
    Route::get('/transactions', TransactionList::class)->name('transactions.index');
    Route::get('/categories', CategoryManager::class)->name('categories.index');
    Route::get('/budgets', BudgetManager::class)->name('budgets.index');
    Route::get('/debts', DebtManager::class)->name('debts.index');

    Route::get('/import/csv', CsvImporter::class)->name('import.csv');
    Route::get('/import/pdf', PdfImporter::class)->name('import.pdf');

    Route::get('/settings', UserSettings::class)->name('settings');

    Route::get('/api/dashboard', [DashboardController::class, 'index'])->name('api.dashboard');
});

require __DIR__ . '/auth.php';
