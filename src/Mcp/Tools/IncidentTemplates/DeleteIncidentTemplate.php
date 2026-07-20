<?php

namespace Cachet\Mcp\Tools\IncidentTemplates;

use Cachet\Actions\IncidentTemplate\DeleteIncidentTemplate as DeleteIncidentTemplateAction;
use Cachet\Mcp\Concerns\GuardsMcpAbilities;
use Cachet\Models\IncidentTemplate;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;

#[IsDestructive]
class DeleteIncidentTemplate extends Tool
{
    use GuardsMcpAbilities;

    protected string $name = 'delete_incident_template';

    protected string $description = 'Delete an incident template. Requires the incident-templates.delete token ability.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->required()->description('The incident template ID.'),
        ];
    }

    public function handle(Request $request, DeleteIncidentTemplateAction $action): Response
    {
        if (! $this->tokenCan('incident-templates.delete')) {
            return $this->missingAbility('incident-templates.delete');
        }

        $template = IncidentTemplate::query()->find($id = $request->integer('id'));

        if ($template === null) {
            return Response::error("Incident template [{$id}] not found.");
        }

        $action->handle($template);

        return Response::text("Incident template [{$id}] deleted.");
    }

    public function shouldRegister(): bool
    {
        return $this->tokenCan('incident-templates.delete');
    }
}
