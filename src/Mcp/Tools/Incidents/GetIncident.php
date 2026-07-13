<?php

namespace Cachet\Mcp\Tools\Incidents;

use Cachet\Mcp\Concerns\PresentsResources;
use Cachet\Models\Incident;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class GetIncident extends Tool
{
    use PresentsResources;

    protected string $name = 'get_incident';

    protected string $description = 'Get a single incident by ID, including its updates and affected components.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->required()->description('The incident ID.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        $incident = Incident::query()->with(['components', 'updates'])->find($id = $request->integer('id'));

        if ($incident === null) {
            return Response::error("Incident [{$id}] not found.");
        }

        return Response::structured(['data' => $this->presentIncident($incident)]);
    }
}
