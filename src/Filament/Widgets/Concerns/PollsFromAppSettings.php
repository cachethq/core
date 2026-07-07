<?php

namespace Cachet\Filament\Widgets\Concerns;

use Cachet\Settings\AppSettings;

trait PollsFromAppSettings
{
    protected function getPollingInterval(): ?string
    {
        $refreshRate = app(AppSettings::class)->refresh_rate;

        return $refreshRate ? max(5, $refreshRate).'s' : null;
    }
}
