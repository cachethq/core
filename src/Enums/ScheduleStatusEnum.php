<?php

namespace Cachet\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ScheduleStatusEnum: int implements HasColor, HasIcon, HasLabel
{
    case upcoming = 0;
    case in_progress = 1;
    case complete = 2;

    /**
     * Get the statuses that are incomplete.
     */
    public static function incomplete(): array
    {
        return [
            self::upcoming->value,
            self::in_progress->value,
        ];
    }

    /**
     * Get the statuses that are in upcoming.
     */
    public static function upcoming(): array
    {
        return [
            self::upcoming->value,
            self::in_progress->value,
        ];
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::upcoming => __('Upcoming'),
            self::in_progress => __('In Progress'),
            self::complete => __('Complete'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::upcoming => 'info',
            self::in_progress => 'warning',
            self::complete => 'success',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::upcoming => 'heroicon-m-calendar-days',
            self::in_progress => 'heroicon-m-ellipsis-horizontal-circle',
            self::complete => 'heroicon-m-check-circle',
        };
    }
}
