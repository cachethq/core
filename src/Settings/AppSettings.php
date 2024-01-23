<?php

namespace Cachet\Settings;

use Spatie\LaravelSettings\Settings;

class AppSettings extends Settings
{
    public string $name = 'Cachet';

    public ?string $about;

    public static function group(): string
    {
        return 'app';
    }
}
