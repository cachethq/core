<?php

namespace Cachet\Filament\Pages;

use Cachet\Settings\CustomizationSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageCustomization extends SettingsPage
{
    protected static string $settings = CustomizationSettings::class;

    protected static ?string $navigationGroup = 'Settings';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->columns(1)->schema([
                    Forms\Components\Textarea::make('header')
                        ->label(__('Custom Header HTML'))
                        ->rows(8)
                        ->extraAttributes(['class' => 'font-mono']),
                    Forms\Components\Textarea::make('footer')
                        ->label(__('Custom Footer HTML'))
                        ->rows(8)
                        ->extraAttributes(['class' => 'font-mono']),
                ]),
                Forms\Components\Section::make()->columns(1)->schema([
                    Forms\Components\Textarea::make('stylesheet')
                        ->label(__('Custom CSS'))
                        ->rows(8)
                        ->extraAttributes(['class' => 'font-mono']),
                ]),
            ]);
    }
}
