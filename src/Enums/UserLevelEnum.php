<?php

declare(strict_types=1);

namespace Cachet\Enums;

enum UserLevelEnum: int
{
    case admin = 1;
    case user = 2;

    /**
     * Get the human-readable name of the enum value.
     */
    public function getName(): string
    {
        return match ($this->value) {
            self::admin->value => __('Admin'),
            self::user->value => __('User'),
        };
    }
}
