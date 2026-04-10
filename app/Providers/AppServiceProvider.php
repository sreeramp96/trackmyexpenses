<?php

namespace App\Providers;

use App\Models\Debt;
use App\Models\Transaction;
use App\Observers\TransactionObserver;
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
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Transaction::observe(TransactionObserver::class);
        Gate::policy(Debt::class, DebtPolicy::class);

        // Set app timezone based on user preference
        if (auth()->check() && auth()->user()->timezone) {
            config(['app.timezone' => auth()->user()->timezone]);
            date_default_timezone_set(auth()->user()->timezone);
        }
    }


}
