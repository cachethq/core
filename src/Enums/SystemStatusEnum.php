<?php

namespace Cachet\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum SystemStatusEnum implements HasColor, HasIcon, HasLabel
{
    case operational;
    case partial_outage;
    case major_outage;
    case under_maintenance;

    public function getLabel(): string
    {
        return match ($this) {
            self::operational => __('cachet::system_status.operational'),
            self::partial_outage => __('cachet::system_status.partial_outage'),
            self::major_outage => __('cachet::system_status.major_outage'),
            self::under_maintenance => __('cachet::system_status.under_maintenance'),
        };
    }

    public function getColor(): array
    {
        return match ($this) {
            self::operational => Color::Green,
            self::partial_outage => Color::Amber,
            self::major_outage => Color::Red,
            self::under_maintenance => Color::Orange,
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::operational => 'heroicon-m-check-circle',
            self::partial_outage => 'cachet-component-partial-outage',
            self::major_outage => 'cachet-component-major-outage',
            self::under_maintenance => 'cachet-component-under-maintenance',
        };
    }
}
