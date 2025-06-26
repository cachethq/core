<?php

namespace Cachet\Filament\Pages\Settings;

use Cachet\Settings\AppSettings;
use Cachet\Settings\CustomizationSettings;
use Filament\Forms;
use Filament\Forms\Form;
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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->columns(2)->schema([
                    Forms\Components\Select::make('locale')
                        ->label(__('cachet::settings.manage_localization.locale_label'))
                        ->options([
                            'es' => 'Spanish',
                            'en' => 'English',
                        ])->searchable()
                        ->suffixIcon('heroicon-o-language'),

                    Forms\Components\Select::make('timezone')
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
                        ->searchable()
                        ->suffixIcon('heroicon-o-globe-alt'),

                    Forms\Components\Toggle::make('show_timezone')
                        ->label(__('cachet::settings.manage_localization.toggles.show_timezone')),
                ]),
            ]);
    }
}
