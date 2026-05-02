<?php

namespace App\Filament\Resources\Categories;

use App\Filament\Resources\Categories\Pages\ManageCategories;
use App\Models\Category;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
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
                    ->required(),
                Select::make('parent_id')
                    ->label('Parent Category')
                    ->relationship('parent', 'name', fn (Builder $query) => $query->where('user_id', Auth::id())->orWhereNull('user_id'))
                    ->searchable()
                    ->native(false)
                    ->placeholder('None'),
                ColorPicker::make('color')
                    ->default('#64748b'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ColorColumn::make('color')
                    ->width(10),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'expense' => 'danger',
                        'income' => 'success',
                        'transfer' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('parent.name')
                    ->label('Parent')
                    ->placeholder('—'),
                TextColumn::make('transactions_count')
                    ->counts('transactions')
                    ->label('Usage'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'expense' => 'Expense',
                        'income' => 'Income',
                        'transfer' => 'Transfer',
                    ]),
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
            'index' => ManageCategories::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }
}
