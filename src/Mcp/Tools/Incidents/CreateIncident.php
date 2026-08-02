<?php

namespace Cachet\Mcp\Tools\Incidents;

use Cachet\Actions\Incident\CreateIncident as CreateIncidentAction;
use Cachet\Data\Requests\Incident\CreateIncidentRequestData;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Mcp\Concerns\GuardsMcpAbilities;
use Cachet\Mcp\Concerns\PresentsResources;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;

class CreateIncident extends Tool
{
    use GuardsMcpAbilities;
    use PresentsResources;

    protected string $name = 'create_incident';

    protected string $description = 'Create a new incident, optionally from an incident template and linking affected components with their new statuses. Requires the incidents.manage token ability.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()->max(255)->required()->description('The name of the incident.'),
            'status' => $schema->integer()
                ->enum(array_column(IncidentStatusEnum::cases(), 'value'))
                ->required()
                ->description('The status: 0 unknown, 1 investigating, 2 identified, 3 watching, 4 fixed.'),
            'message' => $schema->string()->description('The incident message, in Markdown. Required unless template is given.'),
            'template' => $schema->string()->description('The slug of an incident template to render the message from.'),
            'template_vars' => $schema->object()->description('Variables passed to the incident template.'),
            'visible' => $schema->integer()
                ->enum(array_column(ResourceVisibilityEnum::cases(), 'value'))
                ->default(ResourceVisibilityEnum::authenticated->value)
                ->description('Who the incident is visible to: 0 authenticated users only, 1 everyone, 2 hidden.'),
            'stickied' => $schema->boolean()->default(false)->description('Whether the incident is stickied to the top of the status page.'),
            'notifications' => $schema->boolean()->default(false)->description('Whether to notify verified subscribers.'),
            'occurred_at' => $schema->string()->description('When the incident occurred, as an ISO-8601 datetime. Defaults to now.'),
            'published_at' => $schema->string()->description('When to publish the incident, as an ISO-8601 datetime. While set in the future the incident is hidden from the status page and public API. Defaults to published immediately.'),
            'components' => $schema->array()
                ->items($schema->object([
                    'id' => $schema->integer()->required()->description('The component ID.'),
                    'status' => $schema->integer()
                        ->enum(array_column(ComponentStatusEnum::cases(), 'value'))
                        ->required()
                        ->description('The status to display for the component while the incident is unresolved: 1 operational, 2 performance issues, 3 partial outage, 4 major outage, 5 unknown, 6 under maintenance. The component\'s own status is left unchanged; the overlay is reflected in its latest_status and reverts when the incident is fixed. Use update_component to change a component\'s own status.'),
                ]))
                ->description('Affected components and the status to display for each while the incident is unresolved.'),
        ];
    }

    /**
     * The message/template interdependency is validated explicitly because
     * older spatie/laravel-data versions skip the data object's rules for
     * absent properties that declare default values.
     */
    public function handle(Request $request, CreateIncidentAction $action): Response|ResponseFactory
    {
        if (! $this->tokenCan('incidents.manage')) {
            return $this->missingAbility('incidents.manage');
        }

        $request->validate([
            'message' => ['required_without:template', 'nullable', 'string'],
            'template' => ['required_without:message', 'nullable', 'string'],
        ]);

        $data = CreateIncidentRequestData::validateAndCreate($request->all());

        $incident = $action->handle($data)->load(['components', 'updates']);

        return Response::structured(['data' => $this->presentIncident($incident)]);
    }

    public function shouldRegister(): bool
    {
        return $this->tokenCan('incidents.manage');
    }
}
