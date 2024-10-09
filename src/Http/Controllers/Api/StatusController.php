<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Status;

class StatusController
{
    /**
     * Get the current system status.
     */
    public function __invoke(Status $status)
    {
        return response()->json([
            'data' => [
                'status' => $status->current()->name,
                'message' => $status->current()->getLabel(),
            ],
        ]);
    }
}
