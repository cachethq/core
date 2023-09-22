<?php

declare(strict_types=1);

namespace Cachet\Enums;

enum IncidentStatusEnum: int
{
    case investigating = 1;
    case identified = 2;
    case watching = 3;
    case fixed = 4;

    /**
     * Get the human-readable name of the enum value.
     */
    public function getName(): string
    {
        return match ($this->value) {
            self::investigating->value => __('Investigating'),
            self::identified->value => __('Identified'),
            self::watching->value => __('Watching'),
            self::fixed->value => __('Fixed'),
        };
    }
}
