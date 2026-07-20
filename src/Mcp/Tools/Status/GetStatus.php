<?php

namespace Cachet\Mcp\Tools\Status;

use Cachet\Mcp\Concerns\PresentsResources;
use Cachet\Status;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class GetStatus extends Tool
{
    use PresentsResources;

    protected string $name = 'get_status';

    protected string $description = 'Get the overall status of the status page, with component and incident counts.';

    public function handle(Status $status): ResponseFactory
    {
        return Response::structured([
            'data' => [
                'status' => $this->presentEnum($status->current()),
                'components' => array_map(intval(...), (array) $status->components()),
                'incidents' => array_map(intval(...), (array) $status->incidents()),
            ],
        ]);
    }
}
