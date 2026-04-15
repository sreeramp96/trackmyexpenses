<?php

namespace App\Filament\Widgets;

use App\Models\Account;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class AccountStats extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        $accounts = Account::where('user_id', Auth::id())
            ->where('is_active', true)
            ->get();

        $stats = [];

        foreach ($accounts as $account) {
            $stats[] = Stat::make($account->name, '₹'.number_format($account->balance, 2))
                ->description(ucfirst(str_replace('_', ' ', $account->type)))
                ->color($account->balance >= 0 ? 'success' : 'danger')
                ->icon($account->type === 'credit_card' ? 'heroicon-m-credit-card' : 'heroicon-m-building-library');
        }

        return $stats;
    }
}
