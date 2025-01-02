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
            ->brandLogoHeight('2rem')
            ->colors([
                'primary' => Color::rgb('rgb(4, 193, 71)'),
                'purple' => Color::Purple,
                'gray' => Color::Zinc,
            ])
            ->favicon('/vendor/cachethq/cachet/favicon.ico')
            ->viteTheme('resources/css/dashboard/theme.css', 'vendor/cachethq/cachet/build')
            ->discoverResources(__DIR__.'/Filament/Resources', 'Cachet\\Filament\\Resources')
            ->discoverPages(__DIR__.'/Filament/Pages', 'Cachet\\Filament\\Pages')
            ->discoverWidgets(__DIR__.'/Filament/Widgets', 'Cachet\\Filament\\Widgets')
            ->navigationGroups([
                NavigationGroup::make()
                    ->label(fn (): string => __('cachet::navigation.settings.label'))
                    ->collapsed()
                    ->icon('cachet-settings'),
                NavigationGroup::make()
                    ->label(fn (): string => __('cachet::navigation.integrations.label'))
                    ->collapsed(),
                NavigationGroup::make()
                    ->label(fn (): string => __('cachet::navigation.resources.label'))
                    ->collapsible(false),
            ])
            ->navigationItems([
                NavigationItem::make()
                    ->label(fn (): string => __('cachet::navigation.resources.items.status_page'))
                    ->url(Cachet::path())
                    ->group(fn (): string => __('cachet::navigation.resources.label'))
                    ->icon('cachet-component-performance-issues'),
                NavigationItem::make()
                    ->label(fn (): string => __('cachet::navigation.resources.items.documentation'))
                    ->url('https://docs.cachethq.io/?ref=cachet-dashboard')
                    ->group(fn (): string => __('cachet::navigation.resources.label'))
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
