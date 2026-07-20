<?php

namespace Cachet\Mcp\Tools\IncidentTemplates;

use Cachet\Actions\IncidentTemplate\CreateIncidentTemplate as CreateIncidentTemplateAction;
use Cachet\Data\Requests\IncidentTemplate\CreateIncidentTemplateRequestData;
use Cachet\Enums\IncidentTemplateEngineEnum;
use Cachet\Mcp\Concerns\GuardsMcpAbilities;
use Cachet\Mcp\Concerns\PresentsResources;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;

class CreateIncidentTemplate extends Tool
{
    use GuardsMcpAbilities;
    use PresentsResources;

    protected string $name = 'create_incident_template';

    protected string $description = 'Create a new incident template. Requires the incident-templates.manage token ability.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()->max(255)->required()->description('The name of the template.'),
            'template' => $schema->string()->required()->description('The template body, rendered with the chosen engine.'),
            'slug' => $schema->string()->description('The template slug. Defaults to a slug generated from the name.'),
            'engine' => $schema->string()
                ->enum(array_column(IncidentTemplateEngineEnum::cases(), 'value'))
                ->description('The template engine: blade or twig.'),
        ];
    }

    public function handle(Request $request, CreateIncidentTemplateAction $action): Response|ResponseFactory
    {
        if (! $this->tokenCan('incident-templates.manage')) {
            return $this->missingAbility('incident-templates.manage');
        }

        $data = CreateIncidentTemplateRequestData::validateAndCreate($request->all());

        return Response::structured(['data' => $this->presentIncidentTemplate($action->handle($data))]);
    }

    public function shouldRegister(): bool
    {
        return $this->tokenCan('incident-templates.manage');
    }
}
