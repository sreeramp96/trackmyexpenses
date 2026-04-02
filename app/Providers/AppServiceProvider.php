<?php

namespace App\Providers;

use App\Models\Debt;
use App\Models\Transaction;
use App\Observers\AccountObserver;
use App\Policies\DebtPolicy;
use App\Services\BudgetService;
use App\Services\DashboardService;
use App\Services\DebtService;
use App\Services\ReportService;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TransactionService::class);
        $this->app->singleton(BudgetService::class);
        $this->app->singleton(DebtService::class);
        $this->app->singleton(DashboardService::class);
        $this->app->singleton(ReportService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Transaction::observe(AccountObserver::class);
        Gate::policy(Debt::class, DebtPolicy::class);
        Transaction::observe(AccountObserver::class);
    }
}
