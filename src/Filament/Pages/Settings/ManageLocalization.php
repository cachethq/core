<?php

namespace Cachet\Filament\Pages\Settings;

use Cachet\Filament\Forms\Components\Toggle;
use Cachet\Filament\Schemas\Components\Section;
use Cachet\Settings\AppSettings;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class ManageLocalization extends SettingsPage
{
    protected static string $settings = AppSettings::class;

    public static function getNavigationGroup(): ?string
    {
        return __('cachet::navigation.settings.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('cachet::navigation.settings.items.manage_localization');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->columns(2)->schema([
                    Select::make('locale')
                        ->label(__('cachet::settings.manage_localization.locale_label'))
                        ->helperText(__('cachet::settings.manage_localization.locale_helper'))
                        ->options(
                            config('cachet.supported_locales', [
                                'en' => 'English',
                            ])
                        )->searchable()
                        ->suffixIcon(Heroicon::OutlinedLanguage),

                    Select::make('timezone')
                        ->label(__('cachet::settings.manage_localization.timezone_label'))
                        ->helperText(__('cachet::settings.manage_localization.timezone_helper'))
                        ->options(fn () => collect([
                            __('cachet::settings.manage_cachet.timezone_other') => [
                                '-' => __('cachet::settings.manage_cachet.browser_default'),
                            ],
                        ])->merge(collect(timezone_identifiers_list())
                            ->mapToGroups(
                                fn ($timezone) => [
                                    Str::of($timezone)
                                        ->before('/')
                                        ->toString() => [$timezone => $timezone],
                                ]
                            )
                            ->map(fn ($group) => $group->collapse())))
                        ->required()
                        ->searchable()
                        ->suffixIcon(Heroicon::OutlinedGlobeAlt),

                    Toggle::make('show_timezone')
                        ->label(__('cachet::settings.manage_localization.toggles.show_timezone'))
                        ->helperText(__('cachet::settings.manage_localization.toggles.show_timezone_helper')),
                ]),
            ]);
    }
}
