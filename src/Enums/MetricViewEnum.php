<?php

declare(strict_types=1);

namespace Cachet\Enums;

enum MetricViewEnum: int
{
    case last_hour = 0;
    case today = 1;
    case week = 2;
    case month = 3;

    /**
     * Get the human-readable name for the metric view.
     */
    public function getName(): string
    {
        return match ($this) {
            self::last_hour => __('Last Hour'),
            self::today => __('Today'),
            self::week => __('Week'),
            self::month => __('Month'),
        };
    }
}
