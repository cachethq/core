<?php

namespace Cachet\Filament\Pages;

use Cachet\Settings\ThemeSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageTheme extends SettingsPage
{
    protected static string $settings = ThemeSettings::class;

    protected static ?string $navigationGroup = 'Settings';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->columns(2)->schema([
                    Forms\Components\FileUpload::make('app_banner')
                        ->label(__('Banner Image'))
                        ->columnSpanFull(),
                ]),

                Forms\Components\Section::make()->columns(2)->schema([
                    Forms\Components\Fieldset::make(__('Light'))->columns(2)->schema([
                        Forms\Components\ColorPicker::make('light_background')
                            ->label(__('Background')),

                        Forms\Components\ColorPicker::make('light_text')
                            ->label(__('Text')),
                    ]),

                    Forms\Components\Fieldset::make(__('Dark'))->columns(2)->schema([
                        Forms\Components\ColorPicker::make('dark_background')
                            ->label(__('Background')),

                        Forms\Components\ColorPicker::make('dark_text')
                            ->label(__('Text')),
                    ]),
                ]),

                Forms\Components\Section::make()->columns(2)->schema([
                    Forms\Components\Select::make('dark_mode')
                        ->label(__('Dark Mode'))
                        ->options([
                            'system' => __('System'),
                            'dark' => __('Dark'),
                            'light' => __('Light'),
                        ])
                        ->columnSpanFull(),
                ]),
            ]);
    }
}
