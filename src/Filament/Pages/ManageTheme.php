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
                    Forms\Components\FileUpload::make('banner_image')
                        ->label(__('Banner Image'))
                        ->columnSpanFull(),
                    Forms\Components\ColorPicker::make('banner_background')
                        ->label(__('Banner Background Color')),
                    Forms\Components\TextInput::make('banner_padding')
                        ->label(__('Banner Padding'))
                        ->placeholder('40px 0'),
                ]),

                Forms\Components\Section::make()->columns(2)->schema([
                    Forms\Components\ColorPicker::make('primary')
                        ->label(__('Primary')),

                    Forms\Components\ColorPicker::make('secondary')
                        ->label(__('Secondary')),
                ]),
            ]);
    }
}
