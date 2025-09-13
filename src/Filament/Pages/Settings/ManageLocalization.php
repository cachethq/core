<?php

namespace Cachet\Filament\Pages\Settings;

use Cachet\Settings\AppSettings;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
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
                        ->options(
                            config('cachet.supported_locales', [
                                'en' => 'English',
                            ])
                        )->searchable()
                        ->suffixIcon('heroicon-o-language'),

                    Select::make('timezone')
                        ->label(__('cachet::settings.manage_localization.timezone_label'))
                        ->options(fn () => collect(timezone_identifiers_list())
                            ->mapToGroups(
                                fn ($timezone) => [
                                    Str::of($timezone)
                                        ->before('/')
                                        ->toString() => [$timezone => $timezone],
                                ]
                            )
                            ->map(fn ($group) => $group->collapse()))
                        ->required()
                        ->searchable()
                        ->suffixIcon('heroicon-o-globe-alt'),

                    Toggle::make('show_timezone')
                        ->label(__('cachet::settings.manage_localization.toggles.show_timezone')),
                ]),
            ]);
    }
}
