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
        $data = app(DashboardService::class)->getDashboardData(Auth::id(), (int) $this->month, (int) $this->year);
        $recentTransactions = Auth::user()->transactions()
            ->with(['account', 'category'])
            ->latest('transaction_date')
            ->limit(8)
            ->get();

        // Prepare charts
        $dailySpendingLabels = $data['daily_trends']->keys()->map(fn ($d) => "Day $d")->toArray();
        $dailySpendingData = $data['daily_trends']->values()->toArray();

        $categoryLabels = collect($data['category_spending'])->pluck('category')->toArray();
        $categoryData = collect($data['category_spending'])->pluck('total')->toArray();
        $categoryColors = collect($data['category_spending'])->pluck('color')->toArray();

        $historicalLabels = collect($data['historical_trends'])->pluck('label')->toArray();
        $historicalIncome = collect($data['historical_trends'])->pluck('income')->toArray();
        $historicalExpense = collect($data['historical_trends'])->pluck('expense')->toArray();

        // Calculate Net Worth
        $accountBalances = Auth::user()->accounts()->sum('balance');
        $netWorth = (float) $accountBalances + $data['debts']['net_debt'];

        return view('livewire.dashboard-overview', array_merge($data, [
            'summary' => $data['summary'],
            'budgetHealth' => $data['budget_health'],
            'debtsSummary' => $data['debts'],
            'categorySpending' => $data['category_spending'],
            'dailyTrends' => [
                'labels' => $dailySpendingLabels,
                'data' => $dailySpendingData,
            ],
            'categoryChart' => [
                'labels' => $categoryLabels,
                'data' => $categoryData,
                'colors' => $categoryColors,
            ],
            'historicalChart' => [
                'labels' => $historicalLabels,
                'income' => $historicalIncome,
                'expense' => $historicalExpense,
            ],
            'netWorth' => $netWorth,
            'budgets' => $data['budgets'],
            'activeDebts' => $data['active_debts'],
            'recentTransactions' => $recentTransactions,
        ]));
    }
}
