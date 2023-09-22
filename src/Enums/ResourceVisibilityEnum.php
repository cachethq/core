<?php

declare(strict_types=1);

namespace Cachet\Enums;

enum ResourceVisibilityEnum: int
{
    case authenticated = 0;
    case guest = 1;
    case hidden = 2;
}
