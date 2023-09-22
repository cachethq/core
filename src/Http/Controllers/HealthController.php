<?php

declare(strict_types=1);

namespace Cachet\Http\Controllers;

class HealthController
{
    public function __invoke(): string
    {
        return 'OK';
    }
}
