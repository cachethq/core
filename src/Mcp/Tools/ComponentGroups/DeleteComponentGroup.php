<?php

namespace Cachet\Mcp\Tools\ComponentGroups;

use Cachet\Actions\ComponentGroup\DeleteComponentGroup as DeleteComponentGroupAction;
use Cachet\Mcp\Concerns\GuardsMcpAbilities;
use Cachet\Models\ComponentGroup;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;

#[IsDestructive]
class DeleteComponentGroup extends Tool
{
    use GuardsMcpAbilities;

    protected string $name = 'delete_component_group';

    protected string $description = 'Delete a component group. Components in the group are kept but detached. Requires the component-groups.delete token ability.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->required()->description('The component group ID.'),
        ];
    }

    public function handle(Request $request, DeleteComponentGroupAction $action): Response
    {
        if (! $this->tokenCan('component-groups.delete')) {
            return $this->missingAbility('component-groups.delete');
        }

        $group = ComponentGroup::query()->find($id = $request->integer('id'));

        if ($group === null) {
            return Response::error("Component group [{$id}] not found.");
        }

        $action->handle($group);

        return Response::text("Component group [{$id}] deleted.");
    }

    public function shouldRegister(): bool
    {
        return $this->tokenCan('component-groups.delete');
    }
}
