<?php

namespace Cachet\Mcp\Concerns;

use Illuminate\Support\Facades\Gate;
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
     * Determine whether the token ability and its resource policy both allow access.
     */
    protected function tokenCanAnd(string $tokenAbility, string $policyAbility, mixed $arguments): bool
    {
        $user = auth('sanctum')->user();

        return $user !== null
            && $user->tokenCan($tokenAbility)
            && Gate::forUser($user)->allows($policyAbility, $arguments);
    }

    /**
     * Create the error response returned when the required token ability is missing.
     */
    protected function missingAbility(string $ability): Response
    {
        return Response::error("Unauthorized. This tool requires an API token with the [{$ability}] ability.");
    }
}
