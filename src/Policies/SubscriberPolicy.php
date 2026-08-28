<?php

namespace Cachet\Policies;

use Cachet\Concerns\CachetUser;
use Cachet\Models\Subscriber;

class SubscriberPolicy
{
    public function viewAny(CachetUser $user): bool
    {
        return $user->isAdmin();
    }

    public function view(CachetUser $user, Subscriber $subscriber): bool
    {
        return $user->isAdmin();
    }

    public function create(CachetUser $user): bool
    {
        return $user->isAdmin();
    }

    public function update(CachetUser $user, Subscriber $subscriber): bool
    {
        return $user->isAdmin();
    }

    public function delete(CachetUser $user, Subscriber $subscriber): bool
    {
        return $user->isAdmin();
    }

    public function deleteAny(CachetUser $user): bool
    {
        return $user->isAdmin();
    }
}
