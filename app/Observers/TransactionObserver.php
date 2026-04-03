<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        DB::transaction(fn () => $this->applyEffect($transaction));
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            // Reverse the OLD effect (using original values)
            $this->reverseEffect($transaction->getOriginal());

            // Apply the NEW effect (using current values)
            $this->applyEffect($transaction);
        });
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $this->reverseEffect($transaction->toArray());
        });
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $this->applyEffect($transaction);
        });
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(Transaction $transaction): void
    {
        //
    }

    // ── Private Helpers ────────────────────────────────────

    private function applyEffect(Transaction $tx): void
    {
        match ($tx->type) {
            'income' => $this->credit($tx->account_id, $tx->amount),
            'expense' => $this->debit($tx->account_id, $tx->amount),
            'transfer' => $this->transfer($tx->account_id, $tx->to_account_id, $tx->amount),
        };
    }

    private function reverseEffect(array $original): void
    {
        match ($original['type']) {
            'income' => $this->debit($original['account_id'], $original['amount']),
            'expense' => $this->credit($original['account_id'], $original['amount']),
            'transfer' => $this->transfer($original['to_account_id'], $original['account_id'], $original['amount']),
        };
    }

    private function credit(int $accountId, float|string $amount): void
    {
        Account::where('id', $accountId)
            ->increment('balance', (float) $amount);
    }

    private function debit(int $accountId, float|string $amount): void
    {
        Account::where('id', $accountId)
            ->decrement('balance', (float) $amount);
    }

    private function transfer(int $fromId, int $toId, float|string $amount): void
    {
        $this->debit($fromId, $amount);
        $this->credit($toId, $amount);
    }
}
