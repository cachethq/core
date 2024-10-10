<?php

namespace Cachet\Filament\Pages;

use Cachet\Settings\AppSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Str;

class ManageCachet extends SettingsPage
{
    protected static string $settings = AppSettings::class;

    protected static ?string $navigationGroup = 'Settings';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->columns(2)->schema([
                    Forms\Components\TextInput::make('name')
                        ->label(__('Site Name'))
                        ->maxLength(255),
                    Forms\Components\MarkdownEditor::make('about')
                        ->label(__('About This Site'))
                        ->columnSpanFull(),

                    Forms\Components\Select::make('timezone')
                        ->label(__('Timezone'))
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
                        ->label(__('Incident Days'))
                        ->minValue(1)
                        ->maxValue(365)
                        ->step(1),

                    Forms\Components\TextInput::make('major_outage_threshold')
                        ->numeric()
                        ->label(__('Major Outage Threshold'))
                        ->minValue(1)
                        ->maxValue(100)
                        ->step(1)
                        ->suffix('%'),

                    Forms\Components\TextInput::make('refresh_rate')
                        ->numeric()
                        ->label(__('Automatically Refresh Page'))
                        ->minValue(0)
                        ->nullable()
                        ->step(1)
                        ->suffix(__('seconds')),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Toggle::make('show_support')
                                ->label(__('Support Cachet')),
                            Forms\Components\Toggle::make('display_graphs')
                                ->label(__('Display Graphs')),
                        ]),
                    Forms\Components\Toggle::make('show_timezone')
                        ->label(__('Show Timezone')),
                    Forms\Components\Toggle::make('only_disrupted_days')
                        ->label(__('Only Show Disrupted Days')),
                    Forms\Components\Toggle::make('dashboard_login_link')
                        ->label(__('Show Dashboard Link')),
                ]),
            ]);
    }
}
