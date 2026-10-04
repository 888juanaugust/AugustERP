<?php

namespace App\Providers\Filament;

use App\Domain\Pengaturan\Preferensi;
use App\Domain\Pengaturan\PreferensiKey;
use App\Filament\Modul;
use App\Filament\Widgets\CompanyPulse;
use App\Http\Controllers\PrintController;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\FontProviders\LocalFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/**
 * The one panel. Its look is docs/design/DESIGN.md (tokens in
 * resources/css/filament/admin/theme.css), its colours overridable per client
 * in config/client.php; its sidebar is the ten standard module groups
 * (App\Filament\Modul). The brand is the company's name once set in
 * Preferences, else the app name.
 */
class AdminPanelProvider extends PanelProvider
{
    /** The company's name from Preferences, else the app name; never fails, the login page needs it before anything else works. */
    public static function brandName(): string
    {
        $company = rescue(fn () => (string) app(Preferensi::class)->get(PreferensiKey::CompanyName), '', false);

        return trim($company) !== '' ? $company : (string) config('app.name');
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile()
            ->multiFactorAuthentication([
                AppAuthentication::make()->recoverable(),
            ])
            ->brandName(fn (): string => self::brandName())
            ->colors(array_merge([
                'primary' => '#2f5bea',
                'gray' => Color::Slate,
                'success' => '#166534',
                'warning' => '#8a5a00',
                'danger' => '#a11d1d',
                'info' => '#2f5bea',
            ], array_filter((array) config('client.theme.colors', []))))
            ->font('Geist Variable', provider: LocalFontProvider::class)
            ->monoFont('Geist Mono Variable', provider: LocalFontProvider::class)
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->darkMode(false)
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('13.25rem')
            ->maxContentWidth(Width::Full)
            ->navigationGroups(Modul::class)
            ->spa()
            ->databaseTransactions()
            ->unsavedChangesAlerts()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                CompanyPulse::class,
                AccountWidget::class,
            ])
            ->routes(fn () => Route::get('/print/{alias}/{id}', PrintController::class)->name('print'))
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
