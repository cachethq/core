<?php

namespace Cachet\Filament\Support;

use Cachet\Settings\AppSettings;

final class CachetTimezone
{
    public static function get(): ?string
    {
        $timezone = rescue(fn () => app(AppSettings::class)->timezone, null, report: false);

        return $timezone === '-' ? null : $timezone;
    }
}
