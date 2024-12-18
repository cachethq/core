<?php

namespace Cachet\Filament\Pages\Settings;

use Cachet\Data\Cachet\ThemeData;
use Cachet\Settings\ThemeSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Support\Colors\Color;

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
                        ->image()
                        ->imageEditor()
                        ->label(__('Banner Image'))
                        ->disk('public')
                        ->columnSpanFull(),
                ]),

                Forms\Components\Section::make()->columns(2)
                    ->heading(__('Status Page Accent'))
                    ->description(__('Customize the accent color of your status page. Cachet can automatically select a matching base color.'))
                    ->schema([
                        Forms\Components\Select::make('accent')
                            ->label(__('Accent Color'))
                            ->options([
                                'cachet' => '<div class="theme-swatch" style="--swatch: 4 193 71"></div> Cachet',
                                ...collect(Color::all())
                                    ->except(ThemeData::GRAYS)
                                    ->map(function (array $shades, string $color) {
                                        $colorName = __(ucwords($color));

                                        return "<div class=\"theme-swatch\" style=\"--swatch: {$shades[400]}\"></div> {$colorName}";
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
                            ->label(__('Base Color'))
                            ->options(function () {
                                return [
                                    ...collect(Color::all())->only(ThemeData::GRAYS)->map(function (array $shades, string $color) {
                                        $colorName = __(ucwords($color));

                                        return "<div class=\"theme-swatch\" style=\"--swatch: {$shades[400]}\"></div> {$colorName}";
                                    }),
                                ];
                            })
                            ->native(false)
                            ->disabled(fn (Forms\Get $get) => $get('accent_pairing') === true)
                            ->allowHtml(),

                        Forms\Components\Toggle::make('accent_pairing')
                            ->label(__('Accent Pairing'))
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
