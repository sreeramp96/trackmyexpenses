<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Console\Command;

class ReSyncAccountBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:re-sync-balances {--account= : Specific account ID to re-sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate account balances based on transaction history';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $accountId = $this->option('account');

        $query = Account::withTrashed();

        if ($accountId) {
            $query->where('id', $accountId);
        }

        $accounts = $query->get();

        if ($accounts->isEmpty()) {
            $this->error('No accounts found.');

            return 1;
        }

        $this->info("Re-syncing balances for {$accounts->count()} account(s)...");

        $this->withProgressBar($accounts, function ($account) {
            // Note: Transactions also use SoftDeletes, so we only sum active transactions.
            $income = Transaction::where('account_id', $account->id)->income()->sum('amount');
            $expense = Transaction::where('account_id', $account->id)->expense()->sum('amount');
            $outgoingTransfers = Transaction::where('account_id', $account->id)->transfer()->sum('amount');
            $incomingTransfers = Transaction::where('to_account_id', $account->id)->transfer()->sum('amount');

            $newBalance = (float) $income - (float) $expense - (float) $outgoingTransfers + (float) $incomingTransfers;

            $account->update(['balance' => $newBalance]);
        });

        $this->newLine();
        $this->info('Success! All balances have been recalculated.');

        return 0;
    }
}
