<?php

namespace Cachet\Filament\Pages\Settings;

use Cachet\Enums\CaptchaProviderEnum;
use Cachet\Settings\CaptchaSettings;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

use function __;

class ManageCaptcha extends SettingsPage
{
    protected static string $settings = CaptchaSettings::class;

    public static function getNavigationGroup(): ?string
    {
        return __('cachet::navigation.settings.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('cachet::navigation.settings.items.manage_captcha');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('cachet::settings.manage_captcha.section_title'))
                    ->description(__('cachet::settings.manage_captcha.section_description'))
                    ->schema([
                        Select::make('provider')
                            ->label(__('cachet::settings.manage_captcha.provider_label'))
                            ->options(CaptchaProviderEnum::class)
                            ->required()
                            ->selectablePlaceholder(false)
                            ->live()
                            ->helperText(fn (Get $get): ?string => self::provider($get) === null
                                ? null
                                : __('cachet::settings.manage_captcha.provider_helpers.'.self::provider($get)->value)),

                        Grid::make(2)
                            ->visible(fn (Get $get): bool => self::provider($get)?->requiresKeys() === true)
                            ->schema([
                                TextInput::make('site_key')
                                    ->label(__('cachet::settings.manage_captcha.site_key_label'))
                                    ->helperText(fn (Get $get): ?HtmlString => ($url = self::provider($get)?->keysUrl()) === null
                                        ? null
                                        : new HtmlString(__('cachet::settings.manage_captcha.keys_helper', [
                                            'url' => '<a href="'.e($url).'" target="_blank" rel="noopener" class="underline">'.e(parse_url($url, PHP_URL_HOST)).'</a>',
                                        ])))
                                    ->maxLength(255)
                                    ->required(fn (Get $get): bool => self::provider($get)?->requiresKeys() === true)
                                    ->autocomplete(false),

                                TextInput::make('secret_key')
                                    ->label(__('cachet::settings.manage_captcha.secret_key_label'))
                                    ->password()
                                    ->revealable()
                                    ->maxLength(255)
                                    ->required(fn (Get $get): bool => self::provider($get)?->requiresKeys() === true)
                                    ->autocomplete(false),
                            ])
                            ->columnSpanFull(),

                        TextInput::make('altcha_complexity')
                            ->label(__('cachet::settings.manage_captcha.altcha_complexity_label'))
                            ->helperText(__('cachet::settings.manage_captcha.altcha_complexity_helper'))
                            ->numeric()
                            ->integer()
                            ->minValue(1000)
                            ->maxValue(1000000)
                            ->required(fn (Get $get): bool => self::provider($get) === CaptchaProviderEnum::altcha)
                            ->visible(fn (Get $get): bool => self::provider($get) === CaptchaProviderEnum::altcha),
                    ]),
            ]);
    }

    /**
     * Resolve the provider currently selected in the form.
     */
    private static function provider(Get $get): ?CaptchaProviderEnum
    {
        $provider = $get('provider');

        return $provider instanceof CaptchaProviderEnum ? $provider : CaptchaProviderEnum::tryFrom((string) $provider);
    }
}
