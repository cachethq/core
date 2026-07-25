<?php

namespace Cachet\Mcp\Tools\Components;

use Cachet\Actions\Component\UpdateComponent as UpdateComponentAction;
use Cachet\Cachet;
use Cachet\Data\Requests\Component\UpdateComponentRequestData;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Mcp\Concerns\GuardsMcpAbilities;
use Cachet\Mcp\Concerns\PresentsResources;
use Cachet\Models\Component;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;

#[IsIdempotent]
class UpdateComponent extends Tool
{
    use GuardsMcpAbilities;
    use PresentsResources;

    protected string $name = 'update_component';

    protected string $description = 'Update an existing component, including changing its status. Requires the components.manage token ability.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->required()->description('The component ID.'),
            'name' => $schema->string()->max(255)->description('The name of the component.'),
            'description' => $schema->string()->description('A description of the component.'),
            'status' => $schema->integer()
                ->enum(array_column(ComponentStatusEnum::cases(), 'value'))
                ->description('The status: 1 operational, 2 performance issues, 3 partial outage, 4 major outage, 5 unknown, 6 under maintenance.'),
            'link' => $schema->string()->description('A link related to the component.'),
            'order' => $schema->integer()->min(0)->description('The display order of the component.'),
            'enabled' => $schema->boolean(),
            'component_group_id' => $schema->integer()->description('The ID of the component group this component belongs to.'),
        ];
    }

    public function handle(Request $request, UpdateComponentAction $action): Response|ResponseFactory
    {
        if (! $this->tokenCan('components.manage')) {
            return $this->missingAbility('components.manage');
        }

        $component = Component::query()->find($id = $request->integer('id'));

        if ($component === null) {
            return Response::error("Component [{$id}] not found.");
        }

        $data = UpdateComponentRequestData::validateAndCreate(
            $request->only(['name', 'description', 'status', 'link', 'order', 'enabled', 'component_group_id'])
        );

        return Response::structured([
            'data' => $this->presentComponent($action->handle($component, $data, Cachet::user())),
        ]);
    }

    public function shouldRegister(): bool
    {
        return $this->tokenCan('components.manage');
    }
}
