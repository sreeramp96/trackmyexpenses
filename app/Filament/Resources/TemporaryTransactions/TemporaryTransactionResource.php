<?php

namespace App\Filament\Resources\TemporaryTransactions;

use App\Filament\Resources\TemporaryTransactions\Pages\ManageTemporaryTransactions;
use App\Models\Account;
use App\Models\Category;
use App\Models\TemporaryTransaction;
use App\Models\Transaction;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TemporaryTransactionResource extends Resource
{
    protected static ?string $model = TemporaryTransaction::class;

    protected static ?string $navigationLabel = 'Import Review';

    protected static ?string $pluralLabel = 'Import Review';

    protected static ?string $modelLabel = 'Staged Transaction';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-magnifying-glass';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('transaction_date')
                    ->native(false)
                    ->required(),
                TextInput::make('note'),
                TextInput::make('amount')->numeric(),
                Select::make('account_id')
                    ->options(Account::where('user_id', Auth::id())->pluck('name', 'id'))
                    ->native(false),
                Select::make('category_id')
                    ->options(Category::where('user_id', Auth::id())->orWhereNull('user_id')->pluck('name', 'id'))
                    ->native(false),
                Select::make('type')
                    ->options([
                        'income' => 'Income',
                        'expense' => 'Expense',
                    ])
                    ->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextInputColumn::make('transaction_date')
                    ->label('Date')
                    ->type('date')
                    // Format the Carbon / datetime string → plain Y-m-d before
                    // it is injected into the <input type="date"> value attribute.
                    ->getStateUsing(
                        fn (TemporaryTransaction $record): ?string => $record->getRawOriginal('transaction_date')
                                ? Carbon::parse($record->getRawOriginal('transaction_date'))
                                    ->format('Y-m-d')
                                : null
                    )
                    // Save the plain Y-m-d string that the browser returns.
                    ->updateStateUsing(
                        fn (TemporaryTransaction $record, string $state) => $record->updateQuietly(['transaction_date' => $state])
                    )
                    ->rules(['required', 'date']),
                TextInputColumn::make('note')
                    ->label('Description')
                    ->rules(['required']),
                SelectColumn::make('category_id')
                    ->label('Category')
                    ->native(false)
                    ->options(Category::where('user_id', Auth::id())->orWhereNull('user_id')->pluck('name', 'id'))
                    ->searchable(),
                SelectColumn::make('account_id')
                    ->label('Account')
                    ->native(false)
                    ->options(Account::where('user_id', Auth::id())->pluck('name', 'id'))
                    ->searchable(),
                SelectColumn::make('type')
                    ->label('Type')
                    ->native(false)
                    ->options([
                        'income' => 'Income',
                        'expense' => 'Expense',
                    ]),
                TextInputColumn::make('amount')
                    ->label('Amount')
                    ->type('number')
                    ->prefix('₹')
                    ->rules(['required', 'numeric']),
            ])
            ->filters([
                //
            ])
            ->toolbarActions([
                BulkAction::make('finalize')
                    ->label('Finalize & Commit')
                    ->color('success')
                    ->icon('heroicon-m-check-badge')
                    ->requiresConfirmation()
                    ->modalHeading('Commit selected transactions?')
                    ->modalDescription('These rows will be moved from staging to your live ledger. This cannot be undone.')
                    ->modalSubmitActionLabel('Yes, commit')
                    ->action(function (Collection $records): void {
                        $imported = 0;
                        $skipped = 0;

                        DB::transaction(function () use ($records, &$imported, &$skipped): void {
                            foreach ($records as $record) {
                                // Guard: skip rows that are still missing required data
                                if (
                                    ! $record->account_id ||
                                    ! $record->transaction_date ||
                                    ! $record->amount ||
                                    ! $record->type
                                ) {
                                    $skipped++;

                                    continue;
                                }

                                Transaction::create([
                                    'user_id' => $record->user_id,
                                    'account_id' => $record->account_id,
                                    'category_id' => $record->category_id,
                                    'type' => $record->type,
                                    'amount' => $record->amount,
                                    'note' => $record->note,
                                    'transaction_date' => Carbon::parse($record->transaction_date)->format('Y-m-d'),
                                    'reference_number' => $record->reference_number ?? null,
                                ]);

                                $record->delete();
                                $imported++;
                            }
                        });

                        $msg = "Imported {$imported} transaction(s) successfully.";
                        if ($skipped > 0) {
                            $msg .= " {$skipped} row(s) skipped (missing required fields).";
                        }

                        Notification::make()
                            ->title('Transactions imported successfully.')
                            ->success()
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),
                BulkAction::make('delete')
                    ->label('Delete Selected')
                    ->color('danger')
                    ->icon('heroicon-m-trash')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->delete()),
            ])
            ->emptyStateHeading('No transactions to review.')
            ->emptyStateDescription('Your review area is clean.')
            ->recordAction(null);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTemporaryTransactions::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }
}
