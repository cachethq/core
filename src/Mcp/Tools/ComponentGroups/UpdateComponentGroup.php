<?php

namespace Cachet\Mcp\Tools\ComponentGroups;

use Cachet\Actions\ComponentGroup\UpdateComponentGroup as UpdateComponentGroupAction;
use Cachet\Data\Requests\ComponentGroup\UpdateComponentGroupRequestData;
use Cachet\Enums\ComponentGroupVisibilityEnum;
use Cachet\Enums\ResourceOrderColumnEnum;
use Cachet\Enums\ResourceOrderDirectionEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Mcp\Concerns\GuardsMcpAbilities;
use Cachet\Mcp\Concerns\PresentsResources;
use Cachet\Models\ComponentGroup;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;

#[IsIdempotent]
class UpdateComponentGroup extends Tool
{
    use GuardsMcpAbilities;
    use PresentsResources;

    protected string $name = 'update_component_group';

    protected string $description = 'Update an existing component group. Requires the component-groups.manage token ability.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->required()->description('The component group ID.'),
            'name' => $schema->string()->max(255)->description('The name of the component group.'),
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

    public function handle(Request $request, UpdateComponentGroupAction $action): Response|ResponseFactory
    {
        if (! $this->tokenCan('component-groups.manage')) {
            return $this->missingAbility('component-groups.manage');
        }

        $group = ComponentGroup::query()->find($id = $request->integer('id'));

        if ($group === null) {
            return Response::error("Component group [{$id}] not found.");
        }

        $data = UpdateComponentGroupRequestData::validateAndCreate(
            $request->only(['name', 'order', 'visible', 'collapsed', 'order_column', 'order_direction', 'components'])
        );

        return Response::structured(['data' => $this->presentComponentGroup($action->handle($group, $data))]);
    }

    public function shouldRegister(): bool
    {
        return $this->tokenCan('component-groups.manage');
    }
}
