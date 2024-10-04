<?php

namespace Cachet\Settings;

use Spatie\LaravelSettings\Settings;

class LocalizationSettings extends Settings
{
    /**
     * The format used when displaying dates.
     */
    public string $date_format = 'Y-m-d';

    /**
     * The format used when displaying timestamps.
     */
    public string $timestamp_format = 'Y-m-d H:i:s';

    /**
     * The default locale used by Cachet.
     */
    public string $locale = 'en';

    public static function group(): string
    {
        return 'localization';
    }
}
