<?php

namespace Cachet\Mcp\Tools\Incidents;

use Cachet\Actions\Incident\UpdateIncident as UpdateIncidentAction;
use Cachet\Data\Requests\Incident\UpdateIncidentRequestData;
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
class UpdateIncident extends Tool
{
    use GuardsMcpAbilities;
    use PresentsResources;

    protected string $name = 'update_incident';

    protected string $description = 'Update an existing incident\'s attributes. To post a new status update to the incident timeline, use record_incident_update instead. Requires the incidents.manage token ability.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->required()->description('The incident ID.'),
            'tags' => $schema->array()->items($schema->string())->description('Replace the incident tags.'),
            'name' => $schema->string()->max(255)->description('The name of the incident.'),
            'message' => $schema->string()->description('The incident message, in Markdown.'),
            'status' => $schema->integer()
                ->enum(array_column(IncidentStatusEnum::cases(), 'value'))
                ->description('The status: 0 unknown, 1 investigating, 2 identified, 3 watching, 4 fixed.'),
            'visible' => $schema->boolean()->description('Whether the incident is visible to guests.'),
            'stickied' => $schema->boolean()->description('Whether the incident is pinned to the top of the status page.'),
            'notifications' => $schema->boolean()->description('Whether to notify verified subscribers.'),
            'occurred_at' => $schema->string()->description('When the incident occurred, as an ISO-8601 datetime.'),
            'published_at' => $schema->string()->description('When to publish the incident, as an ISO-8601 datetime. While set in the future the incident is hidden from the status page and public API.'),
        ];
    }

    public function handle(Request $request, UpdateIncidentAction $action): Response|ResponseFactory
    {
        if (! $this->tokenCan('incidents.manage')) {
            return $this->missingAbility('incidents.manage');
        }

        $incident = Incident::query()->find($id = $request->integer('id'));

        if ($incident === null) {
            return Response::error("Incident [{$id}] not found.");
        }

        $data = UpdateIncidentRequestData::validateAndCreate(
            $request->only(['tags', 'name', 'message', 'status', 'visible', 'stickied', 'notifications', 'occurred_at', 'published_at'])
        );

        return Response::structured(['data' => $this->presentIncident($action->handle($incident, $data))]);
    }

    public function shouldRegister(): bool
    {
        return $this->tokenCan('incidents.manage');
    }
}
