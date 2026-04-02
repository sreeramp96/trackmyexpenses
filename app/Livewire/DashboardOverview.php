<?php

namespace App\Livewire;

use App\Services\DashboardService;
use App\Services\ReportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Response;
use Livewire\Component;

class DashboardOverview extends Component
{
    //    public function __construct(private DashboardService $dashboardService) {}

    public $month;

    public $year;

    public bool $showModal = false;

    protected $queryString = ['month', 'year'];

    protected $listeners = ['transactionSaved' => '$refresh'];

    public function mount()
    {
        $this->month = $this->month ?? now()->month;
        $this->year = $this->year ?? now()->year;
    }

    public function downloadCsv()
    {

        $key = 'csv-dl:'.Auth::id();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            session()->flash('error', 'Too many downloads. Please wait a minute.');
            return;
        }

        RateLimiter::hit($key, 60);

        $reportService = app(ReportService::class);
        $csvData = $reportService->getCsvData(Auth::id(), (int) $this->month, (int) $this->year);

        $filename = "report-{$this->year}-{$this->month}.csv";

        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }

    public function render()
    {
        $data = app(DashboardService::class)->getDashboardData(Auth::id(), $this->month, $this->year);
        $recentTransactions = Auth::user()->transactions()
            ->with(['account', 'category'])
            ->latest('transaction_date')
            ->limit(8)
            ->get();

        return view('livewire.dashboard-overview', array_merge($data, [
            'summary' => $data['summary'],
            'budgetHealth' => $data['budget_health'],
            'debtsSummary' => $data['debts'],
            'categorySpending' => $data['category_spending'],
            'dailySparkline' => $data['daily_sparkline'],
            'budgets' => $data['budgets'],
            'activeDebts' => $data['active_debts'],
            'recentTransactions' => $recentTransactions,
        ]));
    }
}
