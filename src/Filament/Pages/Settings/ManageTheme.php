<?php

namespace Cachet\Filament\Pages\Settings;

use Cachet\Data\Cachet\ThemeData;
use Cachet\Settings\ThemeSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;

class ManageTheme extends SettingsPage
{
    protected static string $settings = ThemeSettings::class;

    public static function getNavigationGroup(): ?string
    {
        return __('cachet::navigation.settings.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('cachet::navigation.settings.items.manage_theme');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->columns(2)->schema([
                    Forms\Components\FileUpload::make('app_banner')
                        ->image()
                        ->imageEditor()
                        ->label(__('cachet::settings.manage_theme.app_banner_label'))
                        ->disk('public')
                        ->columnSpanFull(),
                ]),

                Forms\Components\Section::make()->columns(2)
                    ->heading(__('cachet::settings.manage_theme.status_page_accent.heading'))
                    ->description(__('cachet::settings.manage_theme.status_page_accent.description'))
                    ->schema([
                        Forms\Components\Select::make('accent')
                            ->label(__('cachet::settings.manage_theme.status_page_accent.accent_color_label'))
                            ->options([
                                ...collect(Color::all())
                                    ->except(ThemeData::GRAYS)
                                    ->prepend(FilamentColor::getColors()['cachet'], 'cachet')
                                    ->map(function (array $shades, string $color) {
                                        $colorName = __(ucwords($color));

                                        return "<div class=\"flex items-center\"><div class=\"theme-swatch\" style=\"--swatch: {$shades[400]}\"></div><div>{$colorName}</div></div>";
                                    }),
                            ])
                            ->native(false)
                            ->allowHtml()
                            ->reactive()
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set, ?string $old, ?string $state) {
                                $accentPairing = $get('accent_pairing');

                                if ($accentPairing) {
                                    $set('accent_content', ThemeData::matchPairing($state));
                                }
                            }),

                        Forms\Components\Select::make('accent_content')
                            ->label(__('cachet::settings.manage_theme.status_page_accent.accent_content_label'))
                            ->options(function () {
                                return [
                                    ...collect(Color::all())->only(ThemeData::GRAYS)->map(function (array $shades, string $color) {
                                        $colorName = __(ucwords($color));

                                        return "<div class=\"flex items-center\"><div class=\"theme-swatch\" style=\"--swatch: {$shades[400]}\"></div><div>{$colorName}</div></div>";
                                    }),
                                ];
                            })
                            ->native(false)
                            ->disabled(fn (Forms\Get $get) => $get('accent_pairing') === true)
                            ->allowHtml(),

                        Forms\Components\Toggle::make('accent_pairing')
                            ->label(__('cachet::settings.manage_theme.status_page_accent.accent_pairing_label'))
                            ->reactive()
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set, ?bool $old, ?bool $state) {
                                $accent = $get('accent');

                                if ($state) {
                                    $set('accent_content', ThemeData::matchPairing($accent));
                                }
                            }),
                    ]),
            ]);
    }
}
