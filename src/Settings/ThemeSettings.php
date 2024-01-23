<?php

namespace Cachet\Settings;

use Spatie\LaravelSettings\Settings;

class ThemeSettings extends Settings
{
    public ?string $banner_image;

    public static function group(): string
    {
        return 'theme';
    }
}
