<?php

namespace Cachet\Mcp\Tools\IncidentTemplates;

use Cachet\Mcp\Concerns\InteractsWithPagination;
use Cachet\Mcp\Concerns\PresentsResources;
use Cachet\Models\IncidentTemplate;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class ListIncidentTemplates extends Tool
{
    use InteractsWithPagination;
    use PresentsResources;

    protected string $name = 'list_incident_templates';

    protected string $description = 'List incident templates, optionally filtered by name. Templates can be used to render the message when creating an incident.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()->description('Filter by partial template name.'),
            'per_page' => $schema->integer()->min(1)->max(100)->default(15),
            'page' => $schema->integer()->min(1)->default(1),
        ];
    }

    public function handle(Request $request): ResponseFactory
    {
        $templates = IncidentTemplate::query()
            ->when($request->filled('name'), fn ($query) => $query->where('name', 'like', '%'.$request->get('name').'%'))
            ->simplePaginate(perPage: $this->perPage($request), page: $this->page($request));

        return Response::structured([
            'data' => $templates->getCollection()->map(fn (IncidentTemplate $template) => $this->presentIncidentTemplate($template))->all(),
            'meta' => $this->paginationMeta($templates),
        ]);
    }
}
