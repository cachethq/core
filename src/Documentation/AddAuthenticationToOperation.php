<?php

namespace Cachet\Documentation;

use Dedoc\Scramble\Extensions\OperationExtension;
use Dedoc\Scramble\Support\Generator\Operation;
use Dedoc\Scramble\Support\RouteInfo;
use Illuminate\Support\Str;

class AddAuthenticationToOperation extends OperationExtension
{
    public function handle(Operation $operation, RouteInfo $routeInfo)
    {
        $hasAuthMiddleware = collect($routeInfo->route->gatherMiddleware())->contains(fn ($m) => Str::startsWith($m, 'auth:'));

        if (! $hasAuthMiddleware) {
            $operation->addSecurity([]);
        }
    }
}
