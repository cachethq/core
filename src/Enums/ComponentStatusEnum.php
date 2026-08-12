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
    case under_maintenance = 6;

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
            self::under_maintenance => __('cachet::component.status.under_maintenance'),
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
            self::under_maintenance => 'cachet-component-under-maintenance',
            default => 'cachet-unknown',
        };
    }

    /**
     * Rank the status by severity, most severe highest.
     */
    public function severity(): int
    {
        return match ($this) {
            self::major_outage => 5,
            self::partial_outage => 4,
            self::performance_issues => 3,
            self::under_maintenance => 2,
            self::unknown => 1,
            self::operational => 0,
        };
    }

    /**
     * Get the text color classes used on the status page.
     */
    public function getTextColorClasses(): string
    {
        return match ($this) {
            self::operational => 'text-green-600 dark:text-green-400',
            self::performance_issues => 'text-purple-600 dark:text-purple-400',
            self::partial_outage => 'text-amber-600 dark:text-amber-400',
            self::major_outage => 'text-red-600 dark:text-red-400',
            self::under_maintenance => 'text-orange-600 dark:text-orange-400',
            default => 'text-blue-600 dark:text-blue-400',
        };
    }

    public function getBadgeColor(): string
    {
        return match ($this) {
            self::operational => 'brightgreen',
            self::performance_issues => 'yellow',
            self::partial_outage => 'orange',
            self::major_outage => 'red',
            self::under_maintenance => 'orange',
            self::unknown => 'lightgray',
        };
    }

    public function getColor(): array
    {
        return match ($this) {
            self::operational => Color::Green,
            self::performance_issues => Color::Purple,
            self::partial_outage => Color::Amber,
            self::major_outage => Color::Red,
            self::under_maintenance => Color::Orange,
            default => Color::Blue,
        };
    }
}
