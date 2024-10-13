<?php

namespace Cachet\Settings;

use Spatie\LaravelSettings\Settings;

class ThemeSettings extends Settings
{
    public ?string $app_banner;
    public ?string $dark_mode;
    public ?string $light_background;
    public ?string $light_text;
    public ?string $dark_background;
    public ?string $dark_text;

    public static function group(): string
    {
        return 'theme';
    }
}
