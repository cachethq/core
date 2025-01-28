<?php

namespace Cachet\Filament\Pages\Settings;

use Cachet\Settings\CustomizationSettings;
use Filament\Forms;
use Filament\Forms\Form;

class ManageCustomization extends SettingsPage
{
    protected static string $settings = CustomizationSettings::class;

    public static function getNavigationGroup(): ?string
    {
        return __('cachet::navigation.settings.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('cachet::navigation.settings.items.manage_customization');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->columns(1)->schema([
                    Forms\Components\Textarea::make('header')
                        ->label(__('cachet::settings.manage_customization.header_label'))
                        ->rows(8)
                        ->extraAttributes(['class' => 'font-mono']),
                    Forms\Components\Textarea::make('footer')
                        ->label(__('cachet::settings.manage_customization.footer_label'))
                        ->rows(8)
                        ->extraAttributes(['class' => 'font-mono']),
                ]),
                Forms\Components\Section::make()->columns(1)->schema([
                    Forms\Components\Textarea::make('stylesheet')
                        ->label(__('cachet::settings.manage_customization.stylesheet_label'))
                        ->rows(8)
                        ->extraAttributes(['class' => 'font-mono']),
                ]),
            ]);
    }
}
