<?php

namespace App\Filament\Widgets;

use App\Models\Account;
use App\Models\Transaction;
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
        return 2;
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

        $balance = Account::where('user_id', $userId)
            ->where('is_active', true)
            ->sum('balance');

        return [
            Stat::make('Income', '₹'.number_format($income, 2))
                ->description(date('F Y', mktime(0, 0, 0, (int) $month, 1, (int) $year)))
                ->color('success'),
            Stat::make('Expenses', '₹'.number_format($expense, 2))
                ->description('For selected period')
                ->color('danger'),
        ];
    }
}
