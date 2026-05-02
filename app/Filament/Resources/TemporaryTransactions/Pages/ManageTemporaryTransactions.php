<?php

namespace App\Filament\Resources\TemporaryTransactions\Pages;

use App\Filament\Resources\TemporaryTransactions\TemporaryTransactionResource;
use App\Models\TemporaryTransaction;
use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManageTemporaryTransactions extends ManageRecords
{
    protected static string $resource = TemporaryTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('commitAll')
                ->label('Commit All Transactions')
                ->color('success')
                ->icon('heroicon-m-check-badge')
                ->requiresConfirmation()
                ->modalHeading('Commit All Staged Transactions')
                ->modalDescription('This will move all valid staged transactions to your main ledger. Continue?')
                ->action(function () {
                    $records = TemporaryTransaction::where('user_id', Auth::id())->get();

                    DB::transaction(function () use ($records) {
                        foreach ($records as $record) {
                            if (! $record->account_id || ! $record->transaction_date || ! $record->amount || ! $record->type) {
                                continue;
                            }

                            Transaction::create([
                                'user_id' => $record->user_id,
                                'account_id' => $record->account_id,
                                'category_id' => $record->category_id,
                                'type' => $record->type,
                                'amount' => $record->amount,
                                'note' => $record->note,
                                'transaction_date' => $record->transaction_date,
                                'reference_number' => $record->reference_number,
                            ]);

                            $record->delete();
                        }
                    });

                    Notification::make()
                        ->title('All transactions committed successfully.')
                        ->success()
                        ->send();
                }),
            Action::make('clearStaging')
                ->label('Clear Review Area')
                ->color('danger')
                ->icon('heroicon-m-x-circle')
                ->requiresConfirmation()
                ->action(fn () => TemporaryTransaction::where('user_id', Auth::id())->delete())
                ->after(fn () => Notification::make()->title('Review area cleared.')->success()->send()),
            CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['user_id'] = Auth::id();

                    return $data;
                }),
        ];
    }
}
