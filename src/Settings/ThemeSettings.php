<?php

namespace Cachet\Settings;

use Spatie\LaravelSettings\Settings;

class ThemeSettings extends Settings
{
    public ?string $app_banner;

    public static function group(): string
    {
        return 'theme';
    }
}
