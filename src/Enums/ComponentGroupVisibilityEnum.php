<?php

declare(strict_types=1);

namespace Cachet\Enums;

enum ComponentGroupVisibilityEnum: int
{
    case expanded = 0;
    case collapsed = 1;
    case collapsed_unless_incident = 2;

    /**
     * Get the human-readable name of the enum value.
     */
    public function getName(): string
    {
        return match ($this->value) {
            self::expanded->value => __('Always expanded'),
            self::collapsed->value => __('Always collapsed'),
            self::collapsed_unless_incident->value => __('Collapsed unless there is an incident in the group'),
        };
    }
}
