<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected string $view = 'filament.pages.settings';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'currency' => Auth::user()->currency,
            'timezone' => Auth::user()->timezone,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('currency')
                    ->options([
                        'INR' => '₹ Indian Rupee',
                        'USD' => '$ US Dollar',
                        'EUR' => '€ Euro',
                        'GBP' => '£ British Pound',
                        'JPY' => '¥ Japanese Yen',
                        'AED' => 'د.إ UAE Dirham',
                    ])
                    ->native(false)
                    ->required(),
                Select::make('timezone')
                    ->options(collect(\DateTimeZone::listIdentifiers())->mapWithKeys(fn ($tz) => [$tz => $tz])->toArray())
                    ->searchable()
                    ->native(false)
                    ->required(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Auth::user()->update($data);

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
}
