<?php

declare(strict_types=1);

namespace Cachet\Events\Beacon;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BeaconSent
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct()
    {
        //
    }
}
