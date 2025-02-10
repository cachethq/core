<?php

namespace Cachet\Enums;

use Filament\Support\Colors\Color;
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

    public static function outage(): array
    {
        return [
            self::performance_issues,
            self::partial_outage,
            self::major_outage,
        ];
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::operational => __('cachet::component.status.operational'),
            self::performance_issues => __('cachet::component.status.performance_issues'),
            self::partial_outage => __('cachet::component.status.partial_outage'),
            self::major_outage => __('cachet::component.status.major_outage'),
            default => __('cachet::component.status.unknown'),
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::operational => 'cachet-circle-check',
            self::performance_issues => 'cachet-component-performance-issues',
            self::partial_outage => 'cachet-component-partial-outage',
            self::major_outage => 'cachet-component-major-outage',
            default => 'cachet-unknown',
        };
    }

    public function getColor(): array
    {
        return match ($this) {
            self::operational => Color::Green,
            self::performance_issues => Color::Purple,
            self::partial_outage => Color::Amber,
            self::major_outage => Color::Red,
            default => Color::Blue,
        };
    }
}
