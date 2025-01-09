<?php

namespace Cachet\Documentation;

use Dedoc\Scramble\Extensions\OperationExtension;
use Dedoc\Scramble\Support\Generator\Operation;
use Dedoc\Scramble\Support\RouteInfo;

class AddAuthenticationToOperation extends OperationExtension
{
    public function handle(Operation $operation, RouteInfo $routeInfo)
    {
        // @todo find out if a route is protected by auth by looking at route's middleware to avoid manually annotating methods

        if (in_array('GET', $routeInfo->route->methods())) {
            $operation->addSecurity([]);
        }
    }
}
