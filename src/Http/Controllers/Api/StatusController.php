<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Status;
use Dedoc\Scramble\Attributes\Group;

#[Group('Cachet')]
class StatusController
{
    /**
     * Get System Status
     */
    public function __invoke(Status $status)
    {
        return response()->json([
            'data' => [
                /** @example "operational" */
                'status' => $status->current()->name,
                /** @example "All Systems Operational" */
                'message' => $status->current()->getLabel(),
            ],
        ]);
    }
}
