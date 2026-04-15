<?php

namespace App\Filament\Resources\Debts;

use App\Filament\Resources\Debts\Pages\ManageDebts;
use App\Models\Account;
use App\Models\Debt;
use App\Services\TransactionService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class DebtResource extends Resource
{
    protected static ?string $model = Debt::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|UnitEnum|null $navigationGroup = 'Planning';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('contact_name')
                    ->required()
                    ->maxLength(255),
                ToggleButtons::make('direction')
                    ->options([
                        'lent' => 'Lent (To Collect)',
                        'borrowed' => 'Borrowed (To Pay)',
                    ])
                    ->colors([
                        'lent' => 'success',
                        'borrowed' => 'danger',
                    ])
                    ->inline()
                    ->required(),
                TextInput::make('amount')
                    ->numeric()
                    ->required()
                    ->prefix('₹')
                    ->reactive()
                    ->afterStateUpdated(fn ($state, $set) => $set('remaining_amount', $state)),
                TextInput::make('remaining_amount')
                    ->numeric()
                    ->required()
                    ->prefix('₹'),
                DatePicker::make('due_date')
                    ->native(false),
                TextInput::make('note')
                    ->maxLength(255),
                Toggle::make('is_settled')
                    ->label('Settled'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('contact_name')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('direction')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'lent' ? 'success' : 'danger')
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                TextColumn::make('remaining_amount')
                    ->money('INR')
                    ->color(fn ($record) => $record->isOverdue() ? 'danger' : 'gray'),
                TextColumn::make('due_date')
                    ->date('M d, Y')
                    ->placeholder('No date'),
                IconColumn::make('is_settled')
                    ->boolean()
                    ->label('Settled'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('recordPayment')
                        ->label('Record Payment')
                        ->icon('heroicon-m-banknotes')
                        ->color('success')
                        ->form([
                            Select::make('account_id')
                                ->label('Payment Account')
                                ->native(false)
                                ->options(fn () => Account::where('user_id', Auth::id())->pluck('name', 'id'))
                                ->required(),
                            TextInput::make('amount')
                                ->numeric()
                                ->required()
                                ->default(fn ($record) => $record->remaining_amount)
                                ->prefix('₹'),
                            DatePicker::make('transaction_date')
                                ->default(now())
                                ->native(false)
                                ->required(),
                        ])
                        ->action(function ($record, array $data) {
                            app(TransactionService::class)->create([
                                'user_id' => Auth::id(),
                                'account_id' => $data['account_id'],
                                'type' => $record->direction === 'lent' ? 'income' : 'expense',
                                'amount' => $data['amount'],
                                'transaction_date' => $data['transaction_date'],
                                'note' => 'Payment for debt: '.$record->contact_name,
                                'debt_id' => $record->id,
                            ]);

                            Notification::make()
                                ->title('Payment recorded successfully')
                                ->success()
                                ->send();
                        })
                        ->visible(fn ($record) => ! $record->is_settled),
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageDebts::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }
}
