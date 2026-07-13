<?php

namespace Cachet\Mcp\Tools\Components;

use Cachet\Mcp\Concerns\PresentsResources;
use Cachet\Models\Component;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class GetComponent extends Tool
{
    use PresentsResources;

    protected string $name = 'get_component';

    protected string $description = 'Get a single component by ID.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->required()->description('The component ID.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        $component = Component::query()->find($id = $request->integer('id'));

        if ($component === null) {
            return Response::error("Component [{$id}] not found.");
        }

        return Response::structured(['data' => $this->presentComponent($component)]);
    }
}
