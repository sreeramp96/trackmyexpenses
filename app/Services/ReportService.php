<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * Generate data for CSV export.
     */
    public function getCsvData(int $userId, int $month, int $year): string
    {
        $transactions = Transaction::where('user_id', $userId)
            ->forMonth($month, $year)
            ->with(['account', 'category'])
            ->latest('transaction_date')
            ->get();

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, ['Date', 'Type', 'Account', 'Category', 'Amount', 'Note', 'Reference']);

        foreach ($transactions as $tx) {
            fputcsv($handle, [
                $tx->transaction_date->format('Y-m-d'),
                ucfirst($tx->type),
                $tx->account?->name ?? 'N/A',
                $tx->category?->name ?? 'Uncategorized',
                $tx->amount,
                $tx->note,
                $tx->reference_number,
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }

    /**
     * Placeholder for PDF data generation.
     * In a real app, we'd use dompdf or snappy.
     */
    public function getReportSummary(int $userId, int $month, int $year): array
    {
        $ts = app(TransactionService::class);
        $bs = app(BudgetService::class);
        $ds = app(DebtService::class);

        return [
            'month' => date('F Y', mktime(0, 0, 0, $month, 1, $year)),
            'summary' => $ts->monthlySummary($userId, $month, $year),
            'categories' => $ts->monthlyExpenseByCategory($userId, $month, $year),
            'budget_health' => $bs->getGlobalBudgetHealth($userId, $month, $year),
            'debts' => $ds->getDebtSummary($userId),
        ];
    }
}
