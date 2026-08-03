<?php

namespace Cachet\Mcp\Tools\ComponentGroups;

use Cachet\Actions\ComponentGroup\CreateComponentGroup as CreateComponentGroupAction;
use Cachet\Data\Requests\ComponentGroup\CreateComponentGroupRequestData;
use Cachet\Enums\ComponentGroupVisibilityEnum;
use Cachet\Enums\ResourceOrderColumnEnum;
use Cachet\Enums\ResourceOrderDirectionEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Mcp\Concerns\GuardsMcpAbilities;
use Cachet\Mcp\Concerns\PresentsResources;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;

class CreateComponentGroup extends Tool
{
    use GuardsMcpAbilities;
    use PresentsResources;

    protected string $name = 'create_component_group';

    protected string $description = 'Create a new component group. Requires the component-groups.manage token ability.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()->max(255)->required()->description('The name of the component group.'),
            'order' => $schema->integer()->min(0)->description('The display order of the component group.'),
            'visible' => $schema->integer()
                ->enum(array_column(ResourceVisibilityEnum::cases(), 'value'))
                ->description('Who the group is visible to: 0 authenticated users only, 1 everyone, 2 hidden.'),
            'collapsed' => $schema->integer()
                ->enum(array_column(ComponentGroupVisibilityEnum::cases(), 'value'))
                ->description('Collapse behaviour: 0 expanded, 1 collapsed, 2 collapsed unless a component has an incident.'),
            'order_column' => $schema->string()
                ->enum(array_column(ResourceOrderColumnEnum::cases(), 'value'))
                ->description('How components within the group are ordered.'),
            'order_direction' => $schema->string()
                ->enum(array_column(ResourceOrderDirectionEnum::cases(), 'value'))
                ->description('Direction for the order column. Required unless order_column is manual.'),
            'components' => $schema->array()
                ->items($schema->integer())
                ->description('IDs of components to attach to the group.'),
        ];
    }

    public function handle(Request $request, CreateComponentGroupAction $action): Response|ResponseFactory
    {
        if (! $this->tokenCan('component-groups.manage')) {
            return $this->missingAbility('component-groups.manage');
        }

        $data = CreateComponentGroupRequestData::validateAndCreate($request->all());

        return Response::structured(['data' => $this->presentComponentGroup($action->handle($data))]);
    }

    public function shouldRegister(): bool
    {
        return $this->tokenCan('component-groups.manage');
    }
}
