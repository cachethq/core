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
}
