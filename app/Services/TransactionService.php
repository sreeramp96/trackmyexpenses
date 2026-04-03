<?php

namespace App\Services;

use App\Models\Debt;
use App\Models\Transaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionService
{
    /**
     * Create a new transaction.
     * The AccountObserver automatically updates balances after save.
     *
     * @throws ValidationException
     */
    public function create(array $data): Transaction
    {
        $this->validateTransferAccounts($data);

        return DB::transaction(function () use ($data) {
            $transaction = Transaction::create([
                'user_id' => $data['user_id'],
                'account_id' => $data['account_id'],
                'to_account_id' => $data['to_account_id'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'debt_id' => $data['debt_id'] ?? null,
                'type' => $data['type'],
                'amount' => $data['amount'],
                'note' => $data['note'] ?? null,
                'transaction_date' => $data['transaction_date'],
                'reference_number' => $data['reference_number'] ?? null,
                'is_reconciled' => $data['is_reconciled'] ?? false,
            ]);

            if ($transaction->debt_id) {
                app(DebtService::class)->syncRemainingAmount($transaction->debt);
            }

            return $transaction;
        });
    }

    /**
     * Update an existing transaction.
     * Observer will reverse the old effect and apply the new one.
     */
    public function update(Transaction $transaction, array $data): Transaction
    {
        $this->validateTransferAccounts($data);
        $oldDebtId = $transaction->debt_id;

        DB::transaction(function () use ($transaction, $data) {
            $transaction->update([
                'account_id' => $data['account_id'],
                'to_account_id' => $data['to_account_id'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'debt_id' => $data['debt_id'] ?? null,
                'type' => $data['type'],
                'amount' => $data['amount'],
                'note' => $data['note'] ?? null,
                'transaction_date' => $data['transaction_date'],
                'reference_number' => $data['reference_number'] ?? null,
                'is_reconciled' => $data['is_reconciled'] ?? false,
            ]);
        });

        $transaction = $transaction->fresh();

        if ($transaction->debt_id) {
            app(DebtService::class)->syncRemainingAmount($transaction->debt);
        }

        if ($oldDebtId && $oldDebtId != $transaction->debt_id) {
            $oldDebt = Debt::find($oldDebtId);
            if ($oldDebt) {
                app(DebtService::class)->syncRemainingAmount($oldDebt);
            }
        }

        return $transaction;
    }

    /**
     * Soft-delete a transaction.
     * Observer reverses balance before delete.
     */
    public function delete(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $debt = $transaction->debt;
            $transaction->delete();

            if ($debt) {
                app(DebtService::class)->syncRemainingAmount($debt);
            }
        });
    }

    /**
     * Monthly summary: total income, expense, and net savings.
     */
    public function monthlySummary(int $userId, int $month, int $year): array
    {
        $base = Transaction::where('user_id', $userId)->forMonth($month, $year);

        $income = (clone $base)->income()->sum('amount');
        $expense = (clone $base)->expense()->sum('amount');

        return [
            'income' => (float) $income,
            'expense' => (float) $expense,
            'net' => (float) $income - (float) $expense,
            'savings_rate' => $income > 0
                ? round(((float) $income - (float) $expense) / (float) $income * 100, 1)
                : 0,
        ];
    }

    /**
     * Expense breakdown by category for a given month.
     */
    public function monthlyExpenseByCategory(int $userId, int $month, int $year): Collection
    {
        return Transaction::with('category')
            ->where('user_id', $userId)
            ->expense()
            ->forMonth($month, $year)
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'category' => $row->category?->name ?? 'Uncategorized',
                'color' => $row->category?->color ?? '#94a3b8',
                'total' => (float) $row->total,
            ]);
    }

    /**
     * Daily expense totals for the current month (for sparklines/charts).
     * Ensures all days of the month are represented.
     */
    public function dailyExpensesForMonth(int $userId, int $month, int $year): Collection
    {
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $expenses = Transaction::where('user_id', $userId)
            ->expense()
            ->forMonth($month, $year)
            ->selectRaw('DAY(transaction_date) as day, SUM(amount) as total')
            ->groupBy('day')
            ->get()
            ->pluck('total', 'day');

        $fullMonth = collect();
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $fullMonth->put($i, (float) ($expenses[$i] ?? 0));
        }

        return $fullMonth;
    }

    /**
     * Historical monthly income/expense totals for trend analysis.
     */
    public function historicalMonthlyTotals(int $userId, int $months = 6): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $summary = $this->monthlySummary($userId, $date->month, $date->year);
            $data[] = [
                'label' => $date->format('M Y'),
                'income' => $summary['income'],
                'expense' => $summary['expense'],
            ];
        }

        return $data;
    }

    // ── Private Guards ─────────────────────────────────────

    /**
     * Transfers must have two different accounts.
     */
    private function validateTransferAccounts(array $data): void
    {
        if (
            $data['type'] === 'transfer' &&
            isset($data['to_account_id']) &&
            (int) $data['account_id'] === (int) $data['to_account_id']
        ) {
            throw ValidationException::withMessages([
                'to_account_id' => 'Source and destination accounts must be different.',
            ]);
        }

        if ($data['type'] === 'transfer' && empty($data['to_account_id'])) {
            throw ValidationException::withMessages([
                'to_account_id' => 'A destination account is required for transfers.',
            ]);
        }
    }
}
