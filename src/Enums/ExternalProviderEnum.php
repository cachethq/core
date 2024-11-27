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
        if ($this === self::OhDear) {
            return match ($status) {
                'resolved' => IncidentStatusEnum::fixed,
                'warning' => IncidentStatusEnum::investigating,
                default => IncidentStatusEnum::unknown,
            };
        }
    }
}
