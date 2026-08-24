<?php

namespace Cachet\Enums;

enum ExternalProviderEnum: string
{
    case OhDear = 'OhDear';

    /**
     * Match the status to the Cachet status.
     */
    public function status(mixed $status): IncidentStatusEnum
    {
        return match ($this) {
            self::OhDear => match ($status) {
                'resolved' => IncidentStatusEnum::fixed,
                'warning' => IncidentStatusEnum::investigating,
                default => IncidentStatusEnum::unknown,
            },
        };
    }

    /**
     * Match the status to the Cachet component status.
     */
    public function componentStatus(mixed $status): ComponentStatusEnum
    {
        return match ($this) {
            self::OhDear => match ($status) {
                'up' => ComponentStatusEnum::operational,
                default => ComponentStatusEnum::partial_outage,
            },
        };
    }
}
