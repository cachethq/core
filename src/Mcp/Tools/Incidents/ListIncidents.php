<?php

namespace Cachet\Mcp\Tools\Incidents;

use Cachet\Concerns\ChecksApiAuthentication;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Mcp\Concerns\InteractsWithPagination;
use Cachet\Mcp\Concerns\PresentsResources;
use Cachet\Models\Incident;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class ListIncidents extends Tool
{
    use ChecksApiAuthentication;
    use InteractsWithPagination;
    use PresentsResources;

    protected string $name = 'list_incidents';

    protected string $description = 'List incidents on the status page, newest first, optionally filtered by name or status.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()->description('Filter by partial incident name.'),
            'status' => $schema->integer()
                ->enum(array_column(IncidentStatusEnum::cases(), 'value'))
                ->description('Filter by status: 0 unknown, 1 investigating, 2 identified, 3 watching, 4 fixed.'),
            'per_page' => $schema->integer()->min(1)->max(100)->default(15),
            'page' => $schema->integer()->min(1)->default(1),
        ];
    }

    public function handle(Request $request): ResponseFactory
    {
        $incidents = Incident::query()
            ->visible($this->isAuthenticated())
            ->when($request->filled('name'), fn ($query) => $query->where('name', 'like', '%'.$request->get('name').'%'))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->integer('status')))
            ->latest()
            ->simplePaginate(perPage: $this->perPage($request), page: $this->page($request));

        return Response::structured([
            'data' => $incidents->getCollection()->map(fn (Incident $incident) => $this->presentIncident($incident))->all(),
            'meta' => $this->paginationMeta($incidents),
        ]);
    }
}
