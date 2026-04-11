<?php

namespace App\Filament\Resources\Budgets;

use App\Filament\Resources\Budgets\Pages;
use App\Models\Budget;
use BackedEnum;
use UnitEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    
    protected static string|UnitEnum|null $navigationGroup = 'Planning';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name', fn (Builder $query) => $query->where('user_id', Auth::id())->orWhereNull('user_id'))
                    ->required()
                    ->searchable(),
                TextInput::make('amount')
                    ->numeric()
                    ->required()
                    ->prefix('₹'),
                Select::make('period')
                    ->options([
                        'monthly' => 'Monthly',
                        'weekly' => 'Weekly',
                        'yearly' => 'Yearly',
                    ])
                    ->default('monthly')
                    ->required(),
                DatePicker::make('start_date')
                    ->required()
                    ->default(now()->startOfMonth()),
                DatePicker::make('end_date'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->weight('bold'),
                TextColumn::make('amount')
                    ->money('INR'),
                ViewColumn::make('usage')
                    ->label('Utilisation')
                    ->view('filament.tables.columns.budget-progress'),
                TextColumn::make('start_date')
                    ->date('M Y')
                    ->label('Period'),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\BulkActionGroup::make([
                    \Filament\Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBudgets::route('/'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }
}
