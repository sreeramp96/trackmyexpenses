<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('TrackMyExpenses')
            ->favicon(url('favicon.ico'))
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login()
            ->registration()
            ->colors([
                'primary' => [
                    50 => '232, 244, 237',
                    100 => '184, 221, 200',
                    200 => '134, 196, 162',
                    300 => '85, 170, 124',
                    400 => '45, 138, 84',
                    500 => '26, 107, 60',   // main green
                    600 => '20, 83, 46',
                    700 => '15, 62, 34',
                    800 => '10, 42, 22',
                    900 => '6, 26, 14',
                    950 => '3, 14, 7',
                ],
                'gray' => Color::Slate,
                'success' => Color::Emerald,
                'danger' => Color::Rose,
                'warning' => Color::Amber,
                'info' => Color::Cyan,
            ])
            ->font('Bricolage Grotesque')
            ->sidebarCollapsibleOnDesktop()
            ->darkMode()
            ->maxContentWidth('full')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                //
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
