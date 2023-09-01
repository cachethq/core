<?php

namespace Cachet\Enums;

enum ScheduleStatusEnum: int
{
    case upcoming = 0;
    case in_progress = 1;
    case complete = 2;

    /**
     * Get the human-readable name of the enum value.
     */
    public function getName(): string
    {
        return match ($this->value) {
            self::upcoming->value => __('Upcoming'),
            self::in_progress->value => __('In Progress'),
            self::complete->value => __('Complete'),
        };
    }

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
}
