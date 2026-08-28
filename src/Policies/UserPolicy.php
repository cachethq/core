<?php

namespace Cachet\Policies;

use Cachet\Cachet;
use Cachet\Concerns\CachetUser;
use Illuminate\Database\Eloquent\Model;

class UserPolicy
{
    public function viewAny(CachetUser $user): bool
    {
        return $user->isAdmin();
    }

    public function view(Model&CachetUser $user, Model&CachetUser $record): bool
    {
        return $user->is($record) || $user->isAdmin();
    }

    public function create(CachetUser $user): bool
    {
        return $user->isAdmin();
    }

    public function update(Model&CachetUser $user, Model&CachetUser $record): bool
    {
        if (Cachet::demoMode()) {
            return false;
        }

        return $user->is($record) || $user->isAdmin();
    }

    public function delete(Model&CachetUser $user, Model&CachetUser $record): bool
    {
        if (! $user->isAdmin() || $user->is($record)) {
            return false;
        }

        if ($record->isAdmin() && $record->newQuery()->where('is_admin', true)->count() <= 1) {
            return false;
        }

        return true;
    }

    public function deleteAny(CachetUser $user): bool
    {
        return $user->isAdmin();
    }

    public function issueFullAccessApiToken(CachetUser $user): bool
    {
        return $user->isAdmin();
    }
}
