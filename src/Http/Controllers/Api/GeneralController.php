<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Cachet;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

#[Group('Cachet', weight: 0)]
class GeneralController extends Controller
{
    /**
     * Test the API
     */
    public function ping(): JsonResponse
    {
        return response()->json(['data' => 'Pong!']);
    }

    /**
     * Get Version
     */
    public function version(): JsonResponse
    {
        return response()->json([
            'data' => [
                /** @var "'3.x-dev'" */
                'version' => Cachet::version(),
            ],
        ]);
    }
}
