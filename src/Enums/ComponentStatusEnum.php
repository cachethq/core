<?php

namespace Cachet\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ComponentStatusEnum: int implements HasColor, HasIcon, HasLabel
{
    case operational = 1;
    case performance_issues = 2;
    case partial_outage = 3;
    case major_outage = 4;
    case unknown = 5;

    public function getLabel(): string
    {
        return match ($this) {
            self::operational => __('Operational'),
            self::performance_issues => __('Performance Issues'),
            self::partial_outage => __('Partial Outage'),
            self::major_outage => __('Major Outage'),
            default => __('Unknown Component Status'),
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::operational => 'cachet-component-operational',
            self::performance_issues => 'cachet-component-performance-issues',
            self::partial_outage => 'cachet-component-partial-outage',
            self::major_outage => 'cachet-component-major-outage',
            default => 'cachet-unknown',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::operational => 'success',
            self::performance_issues => 'info',
            self::partial_outage => 'warning',
            self::major_outage => 'danger',
            default => 'secondary',
        };
    }
}
