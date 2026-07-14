<?php

namespace Cachet\Settings;

use Cachet\Enums\ThemeModeEnum;
use Spatie\LaravelSettings\Settings;

class ThemeSettings extends Settings
{
    public ?string $app_banner;

    public ?string $accent;

    public ?string $accent_content;

    public bool $accent_pairing = true;

    public ThemeModeEnum $theme_mode = ThemeModeEnum::auto;

    public static function group(): string
    {
        return 'theme';
    }
}
