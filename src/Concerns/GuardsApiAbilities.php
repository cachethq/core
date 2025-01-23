<?php

namespace Cachet\Concerns;

use Cachet\Models\User;
use Laravel\Sanctum\Exceptions\MissingAbilityException;

trait GuardsApiAbilities
{
    public function guard(string $ability): void
    {
        /** @var User $user */
        $user = auth()->user();

        if (! $user->tokenCan($ability)) {
            throw new MissingAbilityException($ability);
        }
    }
}
