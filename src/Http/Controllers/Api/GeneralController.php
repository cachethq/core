<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Cachet;
use Illuminate\Http\JsonResponse;
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
