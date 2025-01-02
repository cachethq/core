<?php

namespace Cachet\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum MetricTypeEnum: int implements HasIcon, HasLabel
{
    case sum = 0;
    case average = 1;

    public function getIcon(): string
    {
        return match ($this) {
            self::sum => 'cachet-metrics',
            self::average => 'cachet-metrics',
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::sum => __('cachet::metric.sum_label'),
            self::average => __('cachet::metric.average_label'),
        };
    }
}
