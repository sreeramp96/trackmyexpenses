<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class LatestTransactions extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $month = $this->filters['month'] ?? now()->month;
        $year = $this->filters['year'] ?? now()->year;

        return $table
            ->query(
                Transaction::where('user_id', Auth::id())
                    ->whereMonth('transaction_date', $month)
                    ->whereYear('transaction_date', $year)
                    ->latest('transaction_date')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('transaction_date')
                    ->date('M d, Y'),
                Tables\Columns\TextColumn::make('note')
                    ->label('Description')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('category.name')
                    ->badge()
                    ->color(fn ($record) => $record->category?->color ?? 'gray'),
                Tables\Columns\TextColumn::make('amount')
                    ->money('INR')
                    ->weight('bold')
                    ->color(fn ($record) => match ($record->type) {
                        'income' => 'success',
                        'expense' => 'danger',
                        'transfer' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($record, $state) => ($record->type === 'expense' ? '−' : '+').$state),
            ])
            ->paginated(false);
    }
}
