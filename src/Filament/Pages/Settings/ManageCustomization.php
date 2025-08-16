<?php

namespace Cachet\Filament\Pages\Settings;

use Cachet\Settings\CustomizationSettings;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

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

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->columns(1)->schema([
                    Textarea::make('header')
                        ->label(__('cachet::settings.manage_customization.header_label'))
                        ->rows(8)
                        ->extraAttributes(['class' => 'font-mono']),
                    Textarea::make('footer')
                        ->label(__('cachet::settings.manage_customization.footer_label'))
                        ->rows(8)
                        ->extraAttributes(['class' => 'font-mono']),
                ]),
                Section::make()->columns(1)->schema([
                    Textarea::make('stylesheet')
                        ->label(__('cachet::settings.manage_customization.stylesheet_label'))
                        ->rows(8)
                        ->extraAttributes(['class' => 'font-mono']),
                ]),
            ]);
    }
}
