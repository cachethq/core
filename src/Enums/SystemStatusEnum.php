<?php

namespace Cachet\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum SystemStatusEnum implements HasColor, HasIcon, HasLabel
{
    case operational;
    case performance_issues;
    case partial_outage;
    case major_outage;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::operational => __('All systems are operational.'),
            self::partial_outage => __('Some systems are experiencing issues.'),
            self::major_outage => __('Some systems are experiencing major issues.'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::operational => 'success',
            self::partial_outage => 'warning',
            self::major_outage => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::operational => 'cachet-incident-fixed',
            self::partial_outage => 'cachet-component-partial-outage',
            self::major_outage => 'cachet-component-major-outage',
            default => 'cachet-unknown',
        };
    }
}
