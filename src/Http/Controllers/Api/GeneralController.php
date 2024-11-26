<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Cachet;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * @group Cachet
 */
class GeneralController extends Controller
{
    /**
     * Test the API
     *
     * @response {
     *     "data": "Pong!"
     * }
     */
    public function ping(): JsonResponse
    {
        return response()->json(['data' => 'Pong!']);
    }

    /**
     * Get Version
     *
     * @response {
     *     "data": {
     *        "version": "3.x-dev"
     *    }
     * }
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
