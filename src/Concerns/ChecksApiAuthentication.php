<?php

declare(strict_types=1);

namespace Cachet\Concerns;

trait ChecksApiAuthentication
{
    /**
     * Determine whether the API caller is authenticated.
     *
     * The API's read routes carry no auth middleware, so the application's
     * default guard never sees bearer tokens. The Sanctum guard resolves
     * both API tokens and first-party sessions.
     */
    protected function isAuthenticated(): bool
    {
        return auth('sanctum')->check();
    }
}
