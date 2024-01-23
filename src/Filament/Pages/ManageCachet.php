<?php

namespace Cachet\Filament\Pages;

use Cachet\Settings\AppSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

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
                ]),
            ]);
    }
}
