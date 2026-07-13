<?php

namespace Cachet\Mcp\Tools\Components;

use Cachet\Actions\Component\DeleteComponent as DeleteComponentAction;
use Cachet\Mcp\Concerns\GuardsMcpAbilities;
use Cachet\Models\Component;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;

#[IsDestructive]
class DeleteComponent extends Tool
{
    use GuardsMcpAbilities;

    protected string $name = 'delete_component';

    protected string $description = 'Delete a component from the status page. Requires the components.delete token ability.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->required()->description('The component ID.'),
        ];
    }

    public function handle(Request $request, DeleteComponentAction $action): Response
    {
        if (! $this->tokenCan('components.delete')) {
            return $this->missingAbility('components.delete');
        }

        $component = Component::query()->find($id = $request->integer('id'));

        if ($component === null) {
            return Response::error("Component [{$id}] not found.");
        }

        $action->handle($component);

        return Response::text("Component [{$id}] deleted.");
    }

    public function shouldRegister(): bool
    {
        return $this->tokenCan('components.delete');
    }
}
