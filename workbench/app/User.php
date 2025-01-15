<?php

namespace Workbench\App;

use Cachet\Models\User as CachetUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workbench\Database\Factories\UserFactory;

class User extends CachetUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return UserFactory::new();
    }
}
