<?php

namespace Cachet\Mcp\Tools\Components;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Mcp\Concerns\InteractsWithPagination;
use Cachet\Mcp\Concerns\PresentsResources;
use Cachet\Mcp\Concerns\ScopesComponentVisibility;
use Cachet\Models\Component;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class ListComponents extends Tool
{
    use InteractsWithPagination;
    use PresentsResources;
    use ScopesComponentVisibility;

    protected string $name = 'list_components';

    protected string $description = 'List components on the status page, ordered by their configured order, optionally filtered by name, status, enabled state, or component group.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()->description('Filter by partial component name.'),
            'status' => $schema->integer()
                ->enum(array_column(ComponentStatusEnum::cases(), 'value'))
                ->description('Filter by status: 1 operational, 2 performance issues, 3 partial outage, 4 major outage, 5 unknown, 6 under maintenance.'),
            'enabled' => $schema->boolean()->default(true)->description('Filter by enabled state. Disabled components are hidden by default and are only visible to authenticated callers.'),
            'component_group_id' => $schema->integer()->description('Filter by component group ID.'),
            'per_page' => $schema->integer()->min(1)->max(100)->default(15),
            'page' => $schema->integer()->min(1)->default(1),
        ];
    }

    public function handle(Request $request): ResponseFactory
    {
        $components = $this->visibleComponents()
            ->when($request->filled('name'), fn ($query) => $query->where('name', 'like', '%'.$request->get('name').'%'))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->integer('status')))
            ->where('enabled', $request->boolean('enabled', true))
            ->when($request->filled('component_group_id'), fn ($query) => $query->where('component_group_id', $request->integer('component_group_id')))
            ->orderBy('order')
            ->simplePaginate(perPage: $this->perPage($request), page: $this->page($request));

        return Response::structured([
            'data' => $components->getCollection()->map(fn (Component $component) => $this->presentComponent($component))->all(),
            'meta' => $this->paginationMeta($components),
        ]);
    }
}
