<?php

namespace Cachet\Mcp\Tools\IncidentTemplates;

use Cachet\Actions\IncidentTemplate\UpdateIncidentTemplate as UpdateIncidentTemplateAction;
use Cachet\Data\Requests\IncidentTemplate\UpdateIncidentTemplateRequestData;
use Cachet\Enums\IncidentTemplateEngineEnum;
use Cachet\Mcp\Concerns\GuardsMcpAbilities;
use Cachet\Mcp\Concerns\PresentsResources;
use Cachet\Models\IncidentTemplate;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;

#[IsIdempotent]
class UpdateIncidentTemplate extends Tool
{
    use GuardsMcpAbilities;
    use PresentsResources;

    protected string $name = 'update_incident_template';

    protected string $description = 'Update an existing incident template. Requires the incident-templates.manage token ability.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->required()->description('The incident template ID.'),
            'name' => $schema->string()->max(255)->description('The name of the template.'),
            'template' => $schema->string()->description('The template body, rendered with the chosen engine.'),
            'slug' => $schema->string()->description('The template slug.'),
            'engine' => $schema->string()
                ->enum(array_column(IncidentTemplateEngineEnum::cases(), 'value'))
                ->description('The template engine: blade or twig.'),
        ];
    }

    public function handle(Request $request, UpdateIncidentTemplateAction $action): Response|ResponseFactory
    {
        if (! $this->tokenCan('incident-templates.manage')) {
            return $this->missingAbility('incident-templates.manage');
        }

        $template = IncidentTemplate::query()->find($id = $request->integer('id'));

        if ($template === null) {
            return Response::error("Incident template [{$id}] not found.");
        }

        $data = UpdateIncidentTemplateRequestData::validateAndCreate(
            $request->only(['name', 'template', 'slug', 'engine'])
        );

        return Response::structured(['data' => $this->presentIncidentTemplate($action->handle($template, $data))]);
    }

    public function shouldRegister(): bool
    {
        return $this->tokenCan('incident-templates.manage');
    }
}
