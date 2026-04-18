<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('month')
                    ->label('Month')
                    ->native(false)
                    ->options([
                        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
                    ])
                    ->default(now()->month),
                Select::make('year')
                    ->label('Year')
                    ->native(false)
                    ->options(collect(range(now()->year - 2, now()->year))->mapWithKeys(fn ($y) => [$y => $y])->toArray())
                    ->default(now()->year),
            ]);
    }
}
