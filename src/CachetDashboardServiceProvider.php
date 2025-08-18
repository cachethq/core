<?php

namespace Cachet;

use Cachet\Filament\Pages\EditProfile;
use Cachet\Http\Middleware\SetAppLocale;
use Cachet\Settings\AppSettings;
use Filament\FontProviders\LocalFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Schemas\Components\Section;
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
        $appSettings = app(AppSettings::class);

        return $panel
            ->id('cachet')
            ->when(
                ! $this->app->runningInConsole() && $appSettings->enable_external_dependencies,
                fn ($panel) => $panel->font('switzer', 'https://fonts.cdnfonts.com/css/switzer'),
                fn ($panel) => $panel->font('ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji" ', provider: LocalFontProvider::class),
            )
            ->default()
            ->login()
            ->passwordReset()
            ->profile(EditProfile::class)
            ->brandLogo(fn () => view('cachet::filament.brand-logo'))
            ->brandLogoHeight('2rem')
            ->colors([
                'primary' => Color::generateV3Palette('rgb(4, 193, 71)'),
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
                NavigationItem::make()
                    ->label(fn (): string => __('cachet::navigation.resources.items.discord'))
                    ->url('https://discord.gg/gUv2qySfCU')
                    ->group(fn (): string => __('cachet::navigation.resources.label'))
                    ->icon('cachet-discord'),
                NavigationItem::make()
                    ->label(fn (): string => __('cachet::navigation.resources.items.sponsor'))
                    ->url('https://github.com/sponsors/cachethq')
                    ->group(fn (): string => __('cachet::navigation.resources.label'))
                    ->icon('heroicon-o-heart'),
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
                SetAppLocale::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->path(Cachet::dashboardPath())
            ->bootUsing(function (): void {
                Section::configureUsing(fn (Section $section) => $section->columnSpanFull());
            });
    }
}
