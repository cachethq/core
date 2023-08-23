<?php

namespace Cachet\Enums;

enum ComponentStatusEnum: int
{
    case operational = 1;
    case performance_issues = 2;
    case partial_outage = 3;
    case major_outage = 4;

    /**
     * Get the human-readable name of the enum value.
     */
    public function getName(): string
    {
        return match ($this->value) {
            self::operational->value => __('Operational'),
            self::performance_issues->value => __('Performance Issues'),
            self::partial_outage->value => __('Partial Outage'),
            self::major_outage->value => __('Major Outage'),
            default => __('Unknown Component Status'),
        };
    }
}
