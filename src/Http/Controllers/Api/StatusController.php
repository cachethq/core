<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Status;

/**
 * @group Cachet
 */
class StatusController
{
    /**
     * Get System Status
     *
     * @response {
     *     "data": {
     *        "status": "operational",
     *        "message": "All Systems Operational"
     *     }
     * }
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
