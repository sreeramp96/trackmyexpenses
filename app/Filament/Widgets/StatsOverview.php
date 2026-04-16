<?php

namespace App\Filament\Widgets;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\RecurringExpense;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        $userId = Auth::id();
        $month = $this->filters['month'] ?? now()->month;
        $year = $this->filters['year'] ?? now()->year;

        $income = Transaction::where('user_id', $userId)
            ->income()
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->sum('amount');

        $expense = Transaction::where('user_id', $userId)
            ->expense()
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->sum('amount');

        $recurring = RecurringExpense::where('user_id', $userId)
            ->where('is_active', true)
            ->where('frequency', 'monthly')
            ->sum('amount');

        return [
            Stat::make('Income', '₹'.number_format($income, 2))
                ->description(date('F Y', mktime(0, 0, 0, (int) $month, 1, (int) $year)))
                ->color('success'),
            Stat::make('Expenses', '₹'.number_format($expense, 2))
                ->description('For selected period')
                ->color('danger'),
            Stat::make('Fixed Outflow', '₹'.number_format($recurring, 2))
                ->description('Monthly subscriptions/bills')
                ->color('info')
                ->icon('heroicon-m-arrow-path'),
        ];
    }
}
