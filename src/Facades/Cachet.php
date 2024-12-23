<?php

namespace Cachet\Facades;

use Illuminate\Support\Facades\Facade;

class Cachet extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        return 'cachet';
    }
}
