<?php

namespace App\Filament\Pages;

use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        $user = Auth::user();
        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'avatar_url' => $user->avatar_url,
            'currency' => $user->currency,
            'timezone' => $user->timezone,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Details')
                    ->description('Update your profile information and avatar.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                FileUpload::make('avatar_url')
                                    ->label('Avatar')
                                    ->avatar()
                                    ->imageEditor()
                                    ->disk('public')
                                    ->directory('avatars')
                                    ->columnSpan(1),
                                Grid::make(1)
                                    ->schema([
                                        TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('email')
                                            ->email()
                                            ->required()
                                            ->unique(User::class, 'email', ignorable: auth()->user()),
                                    ])
                                    ->columnSpan(1),
                            ]),
                    ]),

                Section::make('Regional Settings')
                    ->description('Configure your local currency and time zone.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
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
                            ]),
                    ]),

                Section::make('Security')
                    ->description('Change your account password.')
                    ->schema([
                        TextInput::make('new_password')
                            ->label('New Password')
                            ->password()
                            ->minLength(8)
                            ->dehydrated(fn ($state) => filled($state))
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state)),
                    ])
                    ->collapsible(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = Auth::user();

        // Handle password update manually if provided
        if (isset($data['new_password'])) {
            $user->password = $data['new_password'];
            unset($data['new_password']);
        }

        $user->update($data);

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
}
