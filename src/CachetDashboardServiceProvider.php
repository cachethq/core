<?php

namespace Cachet;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class CachetDashboardServiceProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('cachet')
            ->font('switzer', 'https://fonts.cdnfonts.com/css/switzer')
            ->default()
            ->login()
            ->passwordReset()
            ->brandLogo(fn () => view('cachet::filament.brand-logo'))
            ->colors([
                'primary' => Color::rgb('rgb(4, 193, 71)'),
                'purple' => Color::Purple,
            ])
            ->favicon('/vendor/cachethq/cachet/favicon.ico')
            ->viteTheme('resources/css/dashboard/theme.css', 'vendor/cachethq/cachet/build')
            ->discoverResources(__DIR__.'/Filament/Resources', 'Cachet\\Filament\\Resources')
            ->discoverPages(__DIR__.'/Filament/Pages', 'Cachet\\Filament\\Pages')
            ->discoverWidgets(__DIR__.'/Filament/Widgets', 'Cachet\\Filament\\Widgets')
            ->navigationGroups([
                NavigationGroup::make('Settings')
                    ->label(__('Settings'))
                    ->collapsed()
                    ->icon('cachet-settings'),
                NavigationGroup::make(__('Resources'))
                    ->collapsible(false),
            ])
            ->navigationItems([
                NavigationItem::make('Status Page')
                    ->label(__('Status Page'))
                    ->url(Cachet::path())
                    ->group(__('Resources'))
                    ->icon('cachet-component-performance-issues'),
                NavigationItem::make('Documentation')
                    ->label(__('Documentation'))
                    ->url('https://docs.cachethq.io/?ref=cachet-dashboard')
                    ->group(__('Resources'))
                    ->icon('heroicon-o-book-open'),
            ])
            ->renderHook(PanelsRenderHook::GLOBAL_SEARCH_AFTER, fn () => view('cachet::filament.widgets.add-incident-button'))
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
            ])
            ->path(Cachet::dashboardPath());
    }
}
