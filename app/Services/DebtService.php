<?php

namespace App\Services;

use App\Models\Debt;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class DebtService
{
    /**
     * Get a summary of all debts.
     */
    public function getDebtSummary(int $userId): array
    {
        $base = Debt::where('user_id', $userId)->unsettled();

        $toCollect = (clone $base)->lent()->sum('remaining_amount');
        $toPay = (clone $base)->borrowed()->sum('remaining_amount');

        return [
            'total_to_collect' => (float)$toCollect,
            'total_to_pay' => (float)$toPay,
            'net_debt' => (float)$toCollect - (float)$toPay,
            'unsettled_count' => (clone $base)->count(),
            'overdue_count' => (clone $base)->overdue()->count(),
        ];
    }

    /**
     * Get list of unsettled debts with details.
     */
    public function getActiveDebts(int $userId): Collection
    {
        return Debt::where('user_id', $userId)
            ->unsettled()
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function ($debt) {
                return [
                    'id' => $debt->id,
                    'contact_name' => $debt->contact_name,
                    'direction' => $debt->direction,
                    'label' => $debt->label(),
                    'original_amount' => (float)$debt->amount,
                    'remaining_amount' => (float)$debt->remaining_amount,
                    'due_date' => $debt->due_date?->format('Y-m-d'),
                    'is_overdue' => $debt->isOverdue(),
                ];
            });
    }

    /**
     * Update debt's remaining balance based on linked transactions.
     * This should be called by an observer or service.
     */
    public function syncRemainingAmount(Debt $debt): void
    {
        $paid = Transaction::where('debt_id', $debt->id)->sum('amount');
        
        $remaining = (float)$debt->amount - (float)$paid;
        
        $debt->update([
            'remaining_amount' => max(0, $remaining),
            'is_settled' => $remaining <= 0,
        ]);
    }
}
