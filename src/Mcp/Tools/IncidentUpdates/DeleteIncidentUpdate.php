<?php

namespace Cachet\Mcp\Tools\IncidentUpdates;

use Cachet\Actions\Update\DeleteUpdate;
use Cachet\Mcp\Concerns\GuardsMcpAbilities;
use Cachet\Models\Incident;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;

#[IsDestructive]
class DeleteIncidentUpdate extends Tool
{
    use GuardsMcpAbilities;

    protected string $name = 'delete_incident_update';

    protected string $description = 'Delete an update from an incident. Requires the incident-updates.delete token ability.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'incident_id' => $schema->integer()->required()->description('The incident ID.'),
            'update_id' => $schema->integer()->required()->description('The incident update ID.'),
        ];
    }

    public function handle(Request $request, DeleteUpdate $action): Response
    {
        if (! $this->tokenCan('incident-updates.delete')) {
            return $this->missingAbility('incident-updates.delete');
        }

        $incident = Incident::query()->find($incidentId = $request->integer('incident_id'));

        if ($incident === null) {
            return Response::error("Incident [{$incidentId}] not found.");
        }

        $update = $incident->updates()->find($updateId = $request->integer('update_id'));

        if ($update === null) {
            return Response::error("Update [{$updateId}] not found on incident [{$incidentId}].");
        }

        $action->handle($update);

        return Response::text("Update [{$updateId}] deleted from incident [{$incidentId}].");
    }

    public function shouldRegister(): bool
    {
        return $this->tokenCan('incident-updates.delete');
    }
}
