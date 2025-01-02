<?php

namespace Cachet\Enums;

use Filament\Support\Colors\Color;
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
            self::upcoming => __('cachet::schedule.status.upcoming'),
            self::in_progress => __('cachet::schedule.status.in_progress'),
            self::complete => __('cachet::schedule.status.complete'),
        };
    }

    public function getColor(): array
    {
        return match ($this) {
            self::upcoming => Color::Blue,
            self::in_progress => Color::Amber,
            self::complete => Color::Green,
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::complete => 'cachet-circle-check',
            default => 'cachet-clock',
        };
    }

    public static function parse(ScheduleStatusEnum|string|null $value): ?self
    {
        if ($value instanceof self) {
            return $value;
        }

        return self::tryFrom($value);
    }
}
