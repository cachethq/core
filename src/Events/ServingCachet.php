<?php

declare(strict_types=1);

namespace Cachet\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;

class ServingCachet
{
    use Dispatchable;

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    public $request;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}
