<?php

namespace Cachet\Mcp\Tools\Components;

use Cachet\Actions\Component\CreateComponent as CreateComponentAction;
use Cachet\Data\Requests\Component\CreateComponentRequestData;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Mcp\Concerns\GuardsMcpAbilities;
use Cachet\Mcp\Concerns\PresentsResources;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;

class CreateComponent extends Tool
{
    use GuardsMcpAbilities;
    use PresentsResources;

    protected string $name = 'create_component';

    protected string $description = 'Create a new component on the status page. Requires the components.manage token ability.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()->max(255)->required()->description('The name of the component.'),
            'description' => $schema->string()->description('A description of the component.'),
            'status' => $schema->integer()
                ->enum(array_column(ComponentStatusEnum::cases(), 'value'))
                ->description('The status: 1 operational, 2 performance issues, 3 partial outage, 4 major outage, 5 unknown, 6 under maintenance.'),
            'link' => $schema->string()->description('A link related to the component.'),
            'order' => $schema->integer()->min(0)->description('The display order of the component.'),
            'enabled' => $schema->boolean()->default(true),
            'component_group_id' => $schema->integer()->description('The ID of the component group this component belongs to.'),
            'tags' => $schema->array()->items($schema->string())->description('Tags to apply to the component.'),
        ];
    }

    public function handle(Request $request, CreateComponentAction $action): Response|ResponseFactory
    {
        if (! $this->tokenCan('components.manage')) {
            return $this->missingAbility('components.manage');
        }

        $data = CreateComponentRequestData::validateAndCreate($request->all());

        return Response::structured(['data' => $this->presentComponent($action->handle($data))]);
    }

    public function shouldRegister(): bool
    {
        return $this->tokenCan('components.manage');
    }
}
