<?php

namespace Cachet\Mcp\Concerns;

use Laravel\Mcp\Response;

trait GuardsMcpAbilities
{
    /**
     * Determine whether the current MCP session holds an API token with the given ability.
     */
    protected function tokenCan(string $ability): bool
    {
        $user = auth('sanctum')->user();

        return $user !== null && $user->tokenCan($ability);
    }

    /**
     * Create the error response returned when the required token ability is missing.
     */
    protected function missingAbility(string $ability): Response
    {
        return Response::error("Unauthorized. This tool requires an API token with the [{$ability}] ability.");
    }
}
