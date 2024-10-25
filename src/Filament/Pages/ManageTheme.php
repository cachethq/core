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
                            ->rgba()
                            ->label(__('Background')),

                        Forms\Components\ColorPicker::make('light_text')
                            ->rgba()
                            ->label(__('Text')),
                    ]),

                    Forms\Components\Fieldset::make(__('Dark'))->columns(2)->schema([
                        Forms\Components\ColorPicker::make('dark_background')
                            ->rgba()
                            ->label(__('Background')),

                        Forms\Components\ColorPicker::make('dark_text')
                            ->rgba()
                            ->label(__('Text')),
                    ]),
                ]),

                Forms\Components\Section::make()->columns(2)->schema([
                    Forms\Components\Fieldset::make(__('Base'))->columns(2)->schema([
                        Forms\Components\ColorPicker::make('white')
                            ->rgba()
                            ->label(__('White')),
                    ]),
                ]),

                Forms\Components\Section::make()->columns(2)->schema([
                    Forms\Components\Fieldset::make(__('Primary'))->columns(2)->schema([
                        Forms\Components\ColorPicker::make('primary_50')
                            ->rgba()
                            ->label(__('50')),
                        Forms\Components\ColorPicker::make('primary_100')
                            ->rgba()
                            ->label(__('100')),
                        Forms\Components\ColorPicker::make('primary_200')
                            ->rgba()
                            ->label(__('200')),
                        Forms\Components\ColorPicker::make('primary_300')
                            ->rgba()
                            ->label(__('300')),
                        Forms\Components\ColorPicker::make('primary_400')
                            ->rgba()
                            ->label(__('400')),
                        Forms\Components\ColorPicker::make('primary_500')
                            ->rgba()
                            ->label(__('500')),
                        Forms\Components\ColorPicker::make('primary_600')
                            ->rgba()
                            ->label(__('600')),
                        Forms\Components\ColorPicker::make('primary_700')
                            ->rgba()
                            ->label(__('700')),
                        Forms\Components\ColorPicker::make('primary_800')
                            ->rgba()
                            ->label(__('800')),
                        Forms\Components\ColorPicker::make('primary_900')
                            ->rgba()
                            ->label(__('900')),
                    ]),
                ]),

                Forms\Components\Section::make()->columns(2)->schema([
                    Forms\Components\Fieldset::make(__('Zinc'))->columns(2)->schema([
                        Forms\Components\ColorPicker::make('zinc_50')
                            ->rgba()
                            ->label(__('50')),
                        Forms\Components\ColorPicker::make('zinc_100')
                            ->rgba()
                            ->label(__('100')),
                        Forms\Components\ColorPicker::make('zinc_200')
                            ->rgba()
                            ->label(__('200')),
                        Forms\Components\ColorPicker::make('zinc_300')
                            ->rgba()
                            ->label(__('300')),
                        Forms\Components\ColorPicker::make('zinc_400')
                            ->rgba()
                            ->label(__('400')),
                        Forms\Components\ColorPicker::make('zinc_500')
                            ->rgba()
                            ->label(__('500')),
                        Forms\Components\ColorPicker::make('zinc_600')
                            ->rgba()
                            ->label(__('600')),
                        Forms\Components\ColorPicker::make('zinc_700')
                            ->rgba()
                            ->label(__('700')),
                        Forms\Components\ColorPicker::make('zinc_800')
                            ->rgba()
                            ->label(__('800')),
                        Forms\Components\ColorPicker::make('zinc_900')
                            ->rgba()
                            ->label(__('900')),
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

                Forms\Components\Section::make()->columns(2)->schema([
                    Forms\Components\TextInput::make('font_family_sans')
                        ->label(__('Font')),
                ]),

            ]);
    }
}
