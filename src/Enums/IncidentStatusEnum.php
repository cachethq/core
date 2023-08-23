<?php

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
            self::investigating->value => 'Investigating',
            self::identified->value => 'Identified',
            self::watching->value => 'Watching',
            self::fixed->value => 'Fixed',
        };
    }
}
