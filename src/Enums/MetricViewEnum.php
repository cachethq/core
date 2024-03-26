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
            self::last_hour => __('Last Hour'),
            self::today => __('Today'),
            self::week => __('Week'),
            self::month => __('Month'),
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::last_hour => 'cachet-metrics',
            self::today => 'heroicon-m-clock',
            self::week => 'heroicon-m-calendar',
            self::month => 'heroicon-m-calendar-days',
        };
    }

    public function getColor(): string|array|null
    {
        return 'info';
    }
}
