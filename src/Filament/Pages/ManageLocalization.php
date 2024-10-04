<?php

namespace Cachet\Filament\Pages;

use Cachet\Settings\LocalizationSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageLocalization extends SettingsPage
{
    protected static string $settings = LocalizationSettings::class;

    protected static ?string $navigationGroup = 'Settings';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->columns(2)->schema([
                    Forms\Components\Select::make('date_format')
                        ->options([
                            'Y-m-d' => 'Y-m-d',
                            'm/d/Y' => 'm/d/Y',
                            'd/m/Y' => 'd/m/Y',
                            'Y/m/d' => 'Y/m/d',
                            'jS F Y' => 'jS F Y',
                        ]),

                    Forms\Components\Select::make('timestamp_format')
                        ->options([
                            'Y-m-d H:i:s' => 'Y-m-d H:i:s',
                            'm/d/Y h:i:s A' => 'm/d/Y h:i:s A',
                            'd/m/Y H:i:s' => 'd/m/Y H:i:s',
                            'Y/m/d H:i:s' => 'Y/m/d H:i:s',
                            'jS F Y H:i:s' => 'jS F Y H:i:s',
                            'jS F Y H:i A' => 'jS F Y H:i A',
                        ]),
                ]),
            ]);
    }
}
