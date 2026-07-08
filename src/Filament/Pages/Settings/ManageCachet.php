<?php

namespace Cachet\Filament\Pages\Settings;

use Cachet\Settings\AppSettings;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

use function __;

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

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->columns(2)->schema([
                    TextInput::make('name')
                        ->label(__('cachet::settings.manage_cachet.site_name_label'))
                        ->helperText(__('cachet::settings.manage_cachet.site_name_helper'))
                        ->maxLength(255),
                    MarkdownEditor::make('about')
                        ->label(__('cachet::settings.manage_cachet.about_this_site_label'))
                        ->helperText(__('cachet::settings.manage_cachet.about_this_site_helper'))
                        ->columnSpanFull(),
                ]),

                Section::make()->columns(3)->schema([
                    TextInput::make('incident_days')
                        ->numeric()
                        ->label(__('cachet::settings.manage_cachet.incident_days_label'))
                        ->helperText(__('cachet::settings.manage_cachet.incident_days_helper'))
                        ->minValue(1)
                        ->maxValue(3650)
                        ->step(1),

                    TextInput::make('major_outage_threshold')
                        ->numeric()
                        ->label(__('cachet::settings.manage_cachet.major_outage_threshold_label'))
                        ->helperText(__('cachet::settings.manage_cachet.major_outage_threshold_helper'))
                        ->minValue(1)
                        ->maxValue(100)
                        ->step(1)
                        ->suffix('%'),

                    TextInput::make('refresh_rate')
                        ->numeric()
                        ->label(__('cachet::settings.manage_cachet.refresh_rate_label'))
                        ->helperText(__('cachet::settings.manage_cachet.refresh_rate_helper'))
                        ->minValue(0)
                        ->nullable()
                        ->step(1)
                        ->suffix(__('cachet::settings.manage_cachet.refresh_rate_label_input_suffix_seconds')),

                    Grid::make(1)
                        ->schema([
                            Toggle::make('recent_incidents_only')
                                ->label(__('cachet::settings.manage_cachet.toggles.recent_incidents_only'))
                                ->helperText(__('cachet::settings.manage_cachet.toggles.recent_incidents_only_helper'))
                                ->reactive(),
                            TextInput::make('recent_incidents_days')
                                ->numeric()
                                ->label(__('cachet::settings.manage_cachet.toggles.recent_incidents_days'))
                                ->helperText(__('cachet::settings.manage_cachet.toggles.recent_incidents_days_helper'))
                                ->minValue(0)
                                ->nullable()
                                ->step(1)
                                ->suffix(__('cachet::settings.manage_cachet.recent_incidents_days_suffix_days'))
                                ->hidden(fn (Get $get) => $get('recent_incidents_only') !== true),
                        ]),
                ]),

                Section::make(__('cachet::settings.manage_cachet.display_settings_title'))
                    ->columns(2)
                    ->schema([
                        Toggle::make('dashboard_login_link')
                            ->label(__('cachet::settings.manage_cachet.toggles.show_dashboard_link'))
                            ->helperText(__('cachet::settings.manage_cachet.toggles.show_dashboard_link_helper')),
                        Toggle::make('show_support')
                            ->label(__('cachet::settings.manage_cachet.toggles.support_cachet'))
                            ->helperText(__('cachet::settings.manage_cachet.toggles.support_cachet_helper')),
                        Toggle::make('display_graphs')
                            ->label(__('cachet::settings.manage_cachet.toggles.display_graphs'))
                            ->helperText(__('cachet::settings.manage_cachet.toggles.display_graphs_helper')),
                        Toggle::make('enable_external_dependencies')
                            ->label(__('cachet::settings.manage_cachet.toggles.enable_external_dependencies'))
                            ->helperText(__('cachet::settings.manage_cachet.toggles.enable_external_dependencies_helper')),
                        Toggle::make('only_disrupted_days')
                            ->label(__('cachet::settings.manage_cachet.toggles.only_show_disrupted_days'))
                            ->helperText(__('cachet::settings.manage_cachet.toggles.only_show_disrupted_days_helper')),
                        Toggle::make('dynamic_favicon')
                            ->label(__('cachet::settings.manage_cachet.toggles.dynamic_favicon'))
                            ->helperText(__('cachet::settings.manage_cachet.toggles.dynamic_favicon_helper')),
                    ]),

                Section::make(__('cachet::settings.manage_cachet.api_settings_title'))
                    ->columns(2)
                    ->schema([
                        Toggle::make('api_enabled')
                            ->label(__('cachet::settings.manage_cachet.toggles.api_enabled'))
                            ->helperText(__('cachet::settings.manage_cachet.toggles.api_enabled_helper'))
                            ->afterStateUpdated(fn (Set $set) => $set('api_protected', false))
                            ->reactive(),
                        Toggle::make('api_protected')
                            ->label(__('cachet::settings.manage_cachet.toggles.api_protected'))
                            ->helperText(__('cachet::settings.manage_cachet.toggles.api_protected_helper'))
                            ->visible(fn (Get $get) => $get('api_enabled') === true),
                    ]),
            ]);
    }
}
