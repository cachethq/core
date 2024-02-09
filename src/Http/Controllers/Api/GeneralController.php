<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Cachet;
use Cachet\Status;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GeneralController extends Controller
{
    /**
     * Ping the API.
     */
    public function ping(): JsonResponse
    {
        return response()->json(['data' => 'Pong!']);
    }

    /**
     * Get the Cachet status and message.
     */
    public function status(Request $request, Status $status): JsonResponse
    {
        return (new \Cachet\Http\Resources\Status($status))
            ->toResponse($request);
    }

    /**
     * Get the Cachet version.
     */
    public function version(): JsonResponse
    {
        return response()->json([
            'data' => [
                'version' => Cachet::version(),
            ],
        ]);
    }
}
