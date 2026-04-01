<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Transaction;
use Illuminate\Support\Collection;

class BudgetService
{
    /**
     * Get a comparison of all active budgets against actual spending for a month.
     */
    public function getBudgetBreakdown(int $userId, int $month, int $year): Collection
    {
        $budgets = Budget::where('user_id', $userId)
            ->active()
            ->with('category')
            ->get();

        return $budgets->map(function (Budget $budget) use ($month, $year) {
            $spent = Transaction::where('user_id', $budget->user_id)
                ->when($budget->category_id, fn($q) => $q->where('category_id', $budget->category_id))
                ->expense()
                ->whereMonth('transaction_date', $month)
                ->whereYear('transaction_date', $year)
                ->sum('amount');

            $amount = (float)$budget->amount;
            $spent = (float)$spent;
            
            return [
                'id' => $budget->id,
                'category' => $budget->category?->name ?? 'All Categories',
                'budgeted_amount' => $amount,
                'spent_amount' => $spent,
                'remaining_amount' => max(0, $amount - $spent),
                'percent_used' => $amount > 0 ? round(($spent / $amount) * 100, 1) : 0,
                'is_over_budget' => $spent > $amount,
            ];
        });
    }

    /**
     * Overall budget health: total budgeted vs total spent.
     */
    public function getGlobalBudgetHealth(int $userId, int $month, int $year): array
    {
        $breakdown = $this->getBudgetBreakdown($userId, $month, $year);
        
        $totalBudgeted = $breakdown->sum('budgeted_amount');
        $totalSpent = $breakdown->sum('spent_amount');

        return [
            'total_budgeted' => $totalBudgeted,
            'total_spent' => $totalSpent,
            'total_remaining' => max(0, $totalBudgeted - $totalSpent),
            'global_percent_used' => $totalBudgeted > 0 ? round(($totalSpent / $totalBudgeted) * 100, 1) : 0,
        ];
    }
}
