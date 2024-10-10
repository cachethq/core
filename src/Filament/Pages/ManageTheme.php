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
                    Forms\Components\ColorPicker::make('primary')
                        ->label(__('Primary'))
                        ->rgba(),

                    Forms\Components\ColorPicker::make('secondary')
                        ->label(__('Secondary'))
                        ->rgba(),
                ]),
            ]);
    }
}
