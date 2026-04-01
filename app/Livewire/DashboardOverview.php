<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\DashboardService;
use App\Services\ReportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class DashboardOverview extends Component
{
    public $month;
    public $year;

    protected $queryString = ['month', 'year'];

    public function mount()
    {
        $this->month = $this->month ?? now()->month;
        $this->year = $this->year ?? now()->year;
    }

    public function downloadCsv()
    {
        $reportService = app(ReportService::class);
        $csvData = $reportService->getCsvData(Auth::id(), (int)$this->month, (int)$this->year);
        
        $filename = "report-{$this->year}-{$this->month}.csv";

        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }

    public function render()
    {
        $dashboardService = app(DashboardService::class);
        $data = $dashboardService->getDashboardData(Auth::id(), (int)$this->month, (int)$this->year);

        return view('livewire.dashboard-overview', [
            'summary' => $data['summary'],
            'budgetHealth' => $data['budget_health'],
            'debtsSummary' => $data['debts'],
            'categorySpending' => $data['category_spending'],
            'dailySparkline' => $data['daily_sparkline'],
            'budgets' => $data['budgets'],
            'activeDebts' => $data['active_debts'],
        ]);
    }
}
