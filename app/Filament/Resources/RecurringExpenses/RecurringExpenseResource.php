<?php

namespace App\Filament\Resources\RecurringExpenses;

use App\Filament\Resources\RecurringExpenses\Pages\ManageRecurringExpenses;
use App\Models\Category;
use App\Models\RecurringExpense;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class RecurringExpenseResource extends Resource
{
    protected static ?string $model = RecurringExpense::class;

    protected static ?string $navigationLabel = 'Subscriptions & Bills';

    protected static ?string $pluralLabel = 'Subscriptions & Bills';

    protected static ?string $modelLabel = 'Subscription/Bill';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

    protected static string|UnitEnum|null $navigationGroup = 'Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Subscription Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. Netflix, Electricity Bill'),
                        TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('₹')
                            ->placeholder('0.00'),
                        Select::make('category_id')
                            ->label('Category')
                            ->options(Category::where('user_id', Auth::id())->orWhereNull('user_id')->pluck('name', 'id'))
                            ->searchable()
                            ->native(false),
                        Select::make('frequency')
                            ->options([
                                'weekly' => 'Weekly',
                                'monthly' => 'Monthly',
                                'yearly' => 'Yearly',
                            ])
                            ->required()
                            ->default('monthly')
                            ->native(false),
                        TextInput::make('due_day')
                            ->label('Day of Month')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(31)
                            ->placeholder('1-31')
                            ->visible(fn (callable $get) => $get('frequency') === 'monthly'),
                        DatePicker::make('start_date')
                            ->native(false),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                        TextInput::make('note')
                            ->maxLength(255),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('category.name')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('amount')
                    ->money('INR')
                    ->sortable()
                    ->weight('black')
                    ->fontFamily('Sans'),
                TextColumn::make('frequency')
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'weekly' => 'info',
                        'monthly' => 'success',
                        'yearly' => 'warning',
                        default => 'gray',
                    }),
                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageRecurringExpenses::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }
}
