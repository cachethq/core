<?php

namespace Cachet\Enums;

use Filament\Support\Contracts\HasLabel;

enum MetricViewEnum: int implements HasLabel
{
    case last_hour = 0;
    case today = 1;
    case week = 2;
    case month = 3;

    /**
     * Get the human-readable name for the metric view.
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::last_hour => __('Last Hour'),
            self::today => __('Today'),
            self::week => __('Week'),
            self::month => __('Month'),
        };
    }
}
