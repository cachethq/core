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
    public ?string $font_family_sans;
    public ?string $zinc_50;
    public ?string $zinc_100;
    public ?string $zinc_200;
    public ?string $zinc_300;
    public ?string $zinc_400;
    public ?string $zinc_500;
    public ?string $zinc_600;
    public ?string $zinc_700;
    public ?string $zinc_800;
    public ?string $zinc_900;
    public ?string $primary_50;
    public ?string $primary_100;
    public ?string $primary_200;
    public ?string $primary_300;
    public ?string $primary_400;
    public ?string $primary_500;
    public ?string $primary_600;
    public ?string $primary_700;
    public ?string $primary_800;
    public ?string $primary_900;
    public ?string $white;

    public static function group(): string
    {
        return 'theme';
    }
}
