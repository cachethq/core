<?php

namespace Cachet\Filament\Resources\Users\Pages;

use Cachet\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
