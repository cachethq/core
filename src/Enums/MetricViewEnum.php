<?php

namespace Cachet\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum MetricViewEnum: int implements HasColor, HasIcon, HasLabel
{
    case last_hour = 0;
    case today = 1;
    case week = 2;
    case month = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::last_hour => __('cachet::metric.view_labels.last_hour'),
            self::today => __('cachet::metric.view_labels.today'),
            self::week => __('cachet::metric.view_labels.week'),
            self::month => __('cachet::metric.view_labels.month'),
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::last_hour => 'cachet-metrics',
            self::today => 'heroicon-m-clock',
            self::week => 'heroicon-m-calendar',
            self::month => 'heroicon-m-calendar-days',
        };
    }

    public function getColor(): string
    {
        return 'info';
    }
}
