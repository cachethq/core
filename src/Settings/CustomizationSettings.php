<?php

namespace Cachet\Settings;

use Spatie\LaravelSettings\Settings;

class CustomizationSettings extends Settings
{
    public ?string $header;

    public ?string $footer;

    public ?string $stylesheet;

    public static function group(): string
    {
        return 'customization';
    }
}
