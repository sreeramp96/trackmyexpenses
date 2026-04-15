<?php

namespace App\Filament\Resources\Transactions;

use App\Filament\Resources\Transactions\Pages\ManageTransactions;
use App\Models\Transaction;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
// use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('transaction_date')
                    ->required()
                    ->native(false)
                    ->default(now()),
                ToggleButtons::make('type')
                    ->options([
                        'expense' => 'Expense',
                        'income' => 'Income',
                        'transfer' => 'Transfer',
                    ])
                    ->colors([
                        'expense' => 'danger',
                        'income' => 'success',
                        'transfer' => 'info',
                    ])
                    ->inline()
                    ->required()
                    ->reactive(),
                TextInput::make('amount')
                    ->numeric()
                    ->required()
                    ->prefix('₹'),
                Select::make('account_id')
                    ->label('Account')
                    ->relationship('account', 'name', fn (Builder $query) => $query->where('user_id', Auth::id()))
                    ->required()
                    ->native(false)
                    ->searchable(),
                Select::make('to_account_id')
                    ->label('To Account')
                    ->relationship('toAccount', 'name', fn (Builder $query) => $query->where('user_id', Auth::id()))
                    ->required(fn ($get) => $get('type') === 'transfer')
                    ->hidden(fn ($get) => $get('type') !== 'transfer')
                    ->searchable()
                    ->native(false)
                    ->different('account_id')
                    ->validationMessages([
                        'different' => 'Source and destination accounts must be different.',
                    ]),
                Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name', fn (Builder $query) => $query->where('user_id', Auth::id())->orWhereNull('user_id'))
                    ->searchable()
                    ->native(false)
                    ->required(fn ($get) => $get('type') !== 'transfer'),
                TextInput::make('note')
                    ->maxLength(255),
                TextInput::make('reference_number')
                    ->maxLength(255),
                Toggle::make('is_reconciled')
                    ->label('Reconciled'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_date')
                    ->date('M d, Y')
                    ->sortable(),
                TextColumn::make('note')
                    ->label('Description')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('category.name')
                    ->badge()
                    ->color(fn ($record) => $record->category?->color ?? 'gray')
                    ->placeholder('Transfer'),
                TextColumn::make('account.name')
                    ->description(fn ($record) => $record->type === 'transfer' ? "→ {$record->toAccount?->name}" : null),
                TextColumn::make('amount')
                    ->money('INR')
                    ->sortable()
                    ->weight('bold')
                    ->color(fn ($record) => match ($record->type) {
                        'income' => 'success',
                        'expense' => 'danger',
                        'transfer' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($record, $state) => ($record->type === 'expense' ? '-' : '+').$state),
                // IconColumn::make('is_reconciled')
                //     ->boolean()
                //     ->label('St.'),
            ])
            ->defaultSort('transaction_date', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'expense' => 'Expense',
                        'income' => 'Income',
                        'transfer' => 'Transfer',
                    ]),
                SelectFilter::make('account_id')
                    ->label('Account')
                    ->relationship('account', 'name', fn (Builder $query) => $query->where('user_id', Auth::id())),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTransactions::route('/'),
            'import' => Pages\ImportTransactions::route('/import'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }
}
