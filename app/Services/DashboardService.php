<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class DashboardService
{
    protected $transactionService;
    protected $budgetService;
    protected $debtService;

    public function __construct(
        TransactionService $transactionService,
        BudgetService $budgetService,
        DebtService $debtService
    ) {
        $this->transactionService = $transactionService;
        $this->budgetService = $budgetService;
        $this->debtService = $debtService;
    }

    /**
     * Get all data needed for the main dashboard view.
     */
    public function getDashboardData(int $userId, ?int $month = null, ?int $year = null): array
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        return [
            'summary' => $this->transactionService->monthlySummary($userId, $month, $year),
            'budget_health' => $this->budgetService->getGlobalBudgetHealth($userId, $month, $year),
            'debts' => $this->debtService->getDebtSummary($userId),
            'category_spending' => $this->transactionService->monthlyExpenseByCategory($userId, $month, $year),
            'daily_sparkline' => $this->transactionService->dailyExpensesForMonth($userId, $month, $year),
            'budgets' => $this->budgetService->getBudgetBreakdown($userId, $month, $year)->take(5),
            'active_debts' => $this->debtService->getActiveDebts($userId)->take(5),
        ];
    }
}
