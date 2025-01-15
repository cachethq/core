<?php

namespace Cachet\Concerns;

interface CachetUser
{
    /**
     * Determine if the user is an admin.
     */
    public function isAdmin(): bool;
}
