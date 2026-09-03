<?php

namespace Cachet\Filament\Pages\Settings;

use Cachet\Cachet;
use Cachet\Data\Cachet\ThemeData;
use Cachet\Filament\Forms\Components\Toggle;
use Cachet\Filament\Schemas\Components\Section;
use Cachet\Settings\ThemeSettings;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;

class ManageTheme extends SettingsPage
{
    protected static string $settings = ThemeSettings::class;

    public static function canAccess(): bool
    {
        return Cachet::canAccessDashboardFeature('themes');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('cachet::navigation.settings.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('cachet::navigation.settings.items.manage_theme');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('cachet::settings.manage_theme.app_banner_label'))->columns(2)->schema([
                    FileUpload::make('app_banner')
                        ->acceptedFileTypes((array) config('cachet.uploads.image_mime_types'))
                        ->maxSize((int) config('cachet.uploads.max_size'))
                        ->preventFilePathTampering()
                        ->imageEditor()
                        ->label(__('cachet::settings.manage_theme.app_banner_label'))
                        ->hiddenLabel()
                        ->helperText(__('cachet::settings.manage_theme.app_banner_helper'))
                        ->disk((string) config('cachet.uploads.disk'))
                        ->columnSpanFull(),
                ]),

                Section::make()->columns(2)
                    ->heading(__('cachet::settings.manage_theme.status_page_accent.heading'))
                    ->description(__('cachet::settings.manage_theme.status_page_accent.description'))
                    ->schema([
                        Select::make('accent')
                            ->label(__('cachet::settings.manage_theme.status_page_accent.accent_color_label'))
                            ->helperText(__('cachet::settings.manage_theme.status_page_accent.accent_color_helper'))
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
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                $accentPairing = $get('accent_pairing');

                                if ($accentPairing) {
                                    $set('accent_content', ThemeData::matchPairing($state));
                                }
                            }),

                        Select::make('accent_content')
                            ->label(__('cachet::settings.manage_theme.status_page_accent.accent_content_label'))
                            ->helperText(__('cachet::settings.manage_theme.status_page_accent.accent_content_helper'))
                            ->options(function () {
                                return [
                                    ...collect(Color::all())->only(ThemeData::GRAYS)->map(function (array $shades, string $color) {
                                        $colorName = __(ucwords($color));

                                        return "<div class=\"flex items-center\"><div class=\"theme-swatch\" style=\"--swatch: {$shades[400]}\"></div><div>{$colorName}</div></div>";
                                    }),
                                ];
                            })
                            ->native(false)
                            ->disabled(fn (Get $get) => $get('accent_pairing') === true)
                            ->allowHtml(),

                        Toggle::make('accent_pairing')
                            ->label(__('cachet::settings.manage_theme.status_page_accent.accent_pairing_label'))
                            ->helperText(__('cachet::settings.manage_theme.status_page_accent.accent_pairing_helper'))
                            ->reactive()
                            ->afterStateUpdated(function (Get $get, Set $set, ?bool $old, ?bool $state) {
                                $accent = $get('accent');

                                if ($state) {
                                    $set('accent_content', ThemeData::matchPairing($accent));
                                }
                            }),
                    ]),
            ]);
    }
}
