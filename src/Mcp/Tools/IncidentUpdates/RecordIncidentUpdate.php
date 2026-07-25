<?php

namespace Cachet\Mcp\Tools\IncidentUpdates;

use Cachet\Actions\Update\CreateUpdate;
use Cachet\Cachet;
use Cachet\Data\Requests\IncidentUpdate\CreateIncidentUpdateRequestData;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Mcp\Concerns\GuardsMcpAbilities;
use Cachet\Mcp\Concerns\PresentsResources;
use Cachet\Models\Incident;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;

class RecordIncidentUpdate extends Tool
{
    use GuardsMcpAbilities;
    use PresentsResources;

    protected string $name = 'record_incident_update';

    protected string $description = 'Post a status update to an existing incident. Setting status to 4 (fixed) resolves the incident and returns its affected components to operational. Requires the incident-updates.manage token ability.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'incident_id' => $schema->integer()->required()->description('The incident ID.'),
            'status' => $schema->integer()
                ->enum(array_column(IncidentStatusEnum::cases(), 'value'))
                ->required()
                ->description('The new status: 0 unknown, 1 investigating, 2 identified, 3 watching, 4 fixed.'),
            'message' => $schema->string()->required()->description('The update message, in Markdown.'),
        ];
    }

    public function handle(Request $request, CreateUpdate $action): Response|ResponseFactory
    {
        if (! $this->tokenCan('incident-updates.manage')) {
            return $this->missingAbility('incident-updates.manage');
        }

        $incident = Incident::query()->find($id = $request->integer('incident_id'));

        if ($incident === null) {
            return Response::error("Incident [{$id}] not found.");
        }

        $data = CreateIncidentUpdateRequestData::validateAndCreate($request->only(['status', 'message']));

        return Response::structured(['data' => $this->presentUpdate($action->handle($incident, $data, Cachet::user()))]);
    }

    public function shouldRegister(): bool
    {
        return $this->tokenCan('incident-updates.manage');
    }
}
