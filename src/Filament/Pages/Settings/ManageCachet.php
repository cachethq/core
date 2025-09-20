<?php

namespace Cachet\Filament\Pages\Settings;

use Cachet\Settings\AppSettings;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
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
                        ->maxLength(255),
                    MarkdownEditor::make('about')
                        ->label(__('cachet::settings.manage_cachet.about_this_site_label'))
                        ->columnSpanFull(),
                ]),

                Section::make()->columns(3)->schema([
                    TextInput::make('incident_days')
                        ->numeric()
                        ->label(__('cachet::settings.manage_cachet.incident_days_label'))
                        ->minValue(1)
                        ->maxValue(3650)
                        ->step(1),

                    TextInput::make('major_outage_threshold')
                        ->numeric()
                        ->label(__('cachet::settings.manage_cachet.major_outage_threshold_label'))
                        ->minValue(1)
                        ->maxValue(100)
                        ->step(1)
                        ->suffix('%'),

                    TextInput::make('refresh_rate')
                        ->numeric()
                        ->label(__('cachet::settings.manage_cachet.refresh_rate_label'))
                        ->minValue(0)
                        ->nullable()
                        ->step(1)
                        ->suffix(__('cachet::settings.manage_cachet.refresh_rate_label_input_suffix_seconds')),

                    Grid::make(1)
                        ->schema([
                            Toggle::make('recent_incidents_only')
                                ->label(__('cachet::settings.manage_cachet.toggles.recent_incidents_only'))
                                ->reactive(),
                            TextInput::make('recent_incidents_days')
                                ->numeric()
                                ->label(__('cachet::settings.manage_cachet.toggles.recent_incidents_days'))
                                ->minValue(0)
                                ->nullable()
                                ->step(1)
                                ->suffix(__('cachet::settings.manage_cachet.recent_incidents_days_suffix_days'))
                                ->hidden(fn (Get $get) => $get('recent_incidents_only') !== true),
                        ]),
                ]),

                Section::make(__('cachet::settings.manage_cachet.display_settings_title'))
                    ->schema([
                        Toggle::make('dashboard_login_link')
                            ->label(__('cachet::settings.manage_cachet.toggles.show_dashboard_link')),
                        Toggle::make('show_support')
                            ->label(__('cachet::settings.manage_cachet.toggles.support_cachet')),
                        Toggle::make('display_graphs')
                            ->label(__('cachet::settings.manage_cachet.toggles.display_graphs')),
                        Toggle::make('enable_external_dependencies')
                            ->label(__('cachet::settings.manage_cachet.toggles.enable_external_dependencies')),
                        Toggle::make('only_disrupted_days')
                            ->label(__('cachet::settings.manage_cachet.toggles.only_show_disrupted_days')),
                    ]),
            ]);
    }
}
