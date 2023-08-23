<?php

namespace Cachet\Http\Controllers;

class HealthController
{
    public function __invoke(): string
    {
        return 'OK';
    }
}
