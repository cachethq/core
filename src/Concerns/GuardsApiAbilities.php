<?php

declare(strict_types=1);

namespace Cachet\Concerns;

use Cachet\Models\User;
use Laravel\Sanctum\Exceptions\MissingAbilityException;

trait GuardsApiAbilities
{
    /** @throws MissingAbilityException */
    public function guard(string $ability): void
    {
        /** @var User $user */
        $user = auth()->user();

        if (! $user->tokenCan($ability)) {
            throw new MissingAbilityException($ability);
        }
    }

    /**
     * Determine whether the API caller holds the given token ability.
     *
     * Unlike guard(), this never throws and resolves the caller through the
     * Sanctum guard, so it is safe to call on the read routes that carry no
     * auth middleware. First-party sessions and read-only tokens return false.
     */
    protected function tokenCan(string $ability): bool
    {
        return (bool) auth('sanctum')->user()?->tokenCan($ability);
    }
}
