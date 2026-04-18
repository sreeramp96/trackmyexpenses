<?php

namespace App\Filament\Widgets;

use App\Services\TransactionService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\Auth;

class CategoryBreakdown extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Monthly Category Breakdown';

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $month = $this->filters['month'] ?? now()->month;
        $year = $this->filters['year'] ?? now()->year;

        $service = app(TransactionService::class);
        $data = $service->monthlyExpenseByCategory(Auth::id(), (int) $month, (int) $year);

        $labels = collect($data)->pluck('category')->toArray();
        $totals = collect($data)->pluck('total')->toArray();
        $colors = collect($data)->pluck('color')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Spent',
                    'data' => $totals,
                    'backgroundColor' => $colors,
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
