<?php

namespace Cachet\Mcp\Tools\Incidents;

use Cachet\Actions\Incident\DeleteIncident as DeleteIncidentAction;
use Cachet\Mcp\Concerns\GuardsMcpAbilities;
use Cachet\Models\Incident;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;

#[IsDestructive]
class DeleteIncident extends Tool
{
    use GuardsMcpAbilities;

    protected string $name = 'delete_incident';

    protected string $description = 'Delete an incident from the status page. Requires the incidents.delete token ability.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->required()->description('The incident ID.'),
        ];
    }

    public function handle(Request $request, DeleteIncidentAction $action): Response
    {
        if (! $this->tokenCan('incidents.delete')) {
            return $this->missingAbility('incidents.delete');
        }

        $incident = Incident::query()->find($id = $request->integer('id'));

        if ($incident === null) {
            return Response::error("Incident [{$id}] not found.");
        }

        $action->handle($incident);

        return Response::text("Incident [{$id}] deleted.");
    }

    public function shouldRegister(): bool
    {
        return $this->tokenCan('incidents.delete');
    }
}
