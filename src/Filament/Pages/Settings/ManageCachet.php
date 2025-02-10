<?php

namespace Cachet\Filament\Pages\Settings;

use Cachet\Settings\AppSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Support\Str;

class ManageCachet extends SettingsPage
{
    protected static string $settings = AppSettings::class;

    public static function getNavigationGroup(): ?string
    {
        return __('cachet::navigation.settings.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('cachet::navigation.settings.items.manage_cachet');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->columns(2)->schema([
                    Forms\Components\TextInput::make('name')
                        ->label(__('cachet::settings.manage_cachet.site_name_label'))
                        ->maxLength(255),
                    Forms\Components\MarkdownEditor::make('about')
                        ->label(__('cachet::settings.manage_cachet.about_this_site_label'))
                        ->columnSpanFull(),

                    Forms\Components\Select::make('timezone')
                        ->label(__('cachet::settings.manage_cachet.timezone_label'))
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

                    Forms\Components\TextInput::make('incident_days')
                        ->numeric()
                        ->label(__('cachet::settings.manage_cachet.incident_days_label'))
                        ->minValue(1)
                        ->maxValue(365)
                        ->step(1),

                    Forms\Components\TextInput::make('major_outage_threshold')
                        ->numeric()
                        ->label(__('cachet::settings.manage_cachet.major_outage_threshold_label'))
                        ->minValue(1)
                        ->maxValue(100)
                        ->step(1)
                        ->suffix('%'),

                    Forms\Components\TextInput::make('refresh_rate')
                        ->numeric()
                        ->label(__('cachet::settings.manage_cachet.refresh_rate_label'))
                        ->minValue(0)
                        ->nullable()
                        ->step(1)
                        ->suffix(__('cachet::settings.manage_cachet.refresh_rate_label_input_suffix_seconds')),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Toggle::make('show_support')
                                ->label(__('cachet::settings.manage_cachet.toggles.support_cachet')),
                            Forms\Components\Toggle::make('display_graphs')
                                ->label(__('cachet::settings.manage_cachet.toggles.display_graphs')),
                        ]),
                    Forms\Components\Toggle::make('show_timezone')
                        ->label(__('cachet::settings.manage_cachet.toggles.show_timezone')),
                    Forms\Components\Toggle::make('only_disrupted_days')
                        ->label(__('cachet::settings.manage_cachet.toggles.only_show_disrupted_days')),
                    Forms\Components\Toggle::make('dashboard_login_link')
                        ->label(__('cachet::settings.manage_cachet.toggles.show_dashboard_link')),
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Toggle::make('recent_incidents_only')
                                ->label(__('cachet::settings.manage_cachet.toggles.recent_incidents_only'))
                                ->reactive(),
                            Forms\Components\TextInput::make('recent_incidents_days')
                                ->numeric()
                                ->label(__('cachet::settings.manage_cachet.toggles.recent_incidents_days'))
                                ->minValue(0)
                                ->nullable()
                                ->step(1)
                                ->suffix(__('cachet::settings.manage_cachet.recent_incidents_days_suffix_days'))
                                ->hidden(fn (Get $get) => $get('recent_incidents_only') !== true),
                        ]),
                ]),
            ]);
    }
}
