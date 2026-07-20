<?php

namespace Cachet\Mcp\Tools\IncidentUpdates;

use Cachet\Actions\Update\EditUpdate;
use Cachet\Data\Requests\IncidentUpdate\EditIncidentUpdateRequestData;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Mcp\Concerns\GuardsMcpAbilities;
use Cachet\Mcp\Concerns\PresentsResources;
use Cachet\Models\Incident;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;

#[IsIdempotent]
class EditIncidentUpdate extends Tool
{
    use GuardsMcpAbilities;
    use PresentsResources;

    protected string $name = 'edit_incident_update';

    protected string $description = 'Edit an existing update on an incident. Requires the incident-updates.manage token ability.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'incident_id' => $schema->integer()->required()->description('The incident ID.'),
            'update_id' => $schema->integer()->required()->description('The incident update ID.'),
            'status' => $schema->integer()
                ->enum(array_column(IncidentStatusEnum::cases(), 'value'))
                ->description('The status: 0 unknown, 1 investigating, 2 identified, 3 watching, 4 fixed.'),
            'message' => $schema->string()->description('The update message, in Markdown.'),
        ];
    }

    public function handle(Request $request, EditUpdate $action): Response|ResponseFactory
    {
        if (! $this->tokenCan('incident-updates.manage')) {
            return $this->missingAbility('incident-updates.manage');
        }

        $incident = Incident::query()->find($incidentId = $request->integer('incident_id'));

        if ($incident === null) {
            return Response::error("Incident [{$incidentId}] not found.");
        }

        $update = $incident->updates()->find($updateId = $request->integer('update_id'));

        if ($update === null) {
            return Response::error("Update [{$updateId}] not found on incident [{$incidentId}].");
        }

        $data = EditIncidentUpdateRequestData::validateAndCreate($request->only(['status', 'message']));

        return Response::structured(['data' => $this->presentUpdate($action->handle($update, $data))]);
    }

    public function shouldRegister(): bool
    {
        return $this->tokenCan('incident-updates.manage');
    }
}
