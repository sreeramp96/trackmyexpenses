<?php

namespace App\Filament\Widgets;

use App\Services\TransactionService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\Auth;

class SpendingTrend extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Daily Spending Trend';
    
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $month = $this->filters['month'] ?? now()->month;
        $year = $this->filters['year'] ?? now()->year;

        $service = app(TransactionService::class);
        $trends = $service->dailyExpensesForMonth(Auth::id(), (int)$month, (int)$year);

        return [
            'datasets' => [
                [
                    'label' => 'Expenses',
                    'data' => $trends->values()->toArray(),
                    'borderColor' => '#991b1b',
                    'backgroundColor' => 'rgba(153, 27, 27, 0.1)',
                    'fill' => 'start',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $trends->keys()->map(fn ($d) => "Day $d")->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
