<?php

namespace Cachet\Mcp\Tools\ComponentGroups;

use Cachet\Mcp\Concerns\InteractsWithPagination;
use Cachet\Mcp\Concerns\PresentsResources;
use Cachet\Models\ComponentGroup;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class ListComponentGroups extends Tool
{
    use InteractsWithPagination;
    use PresentsResources;

    protected string $name = 'list_component_groups';

    protected string $description = 'List component groups and their components, ordered by their configured order, optionally filtered by name.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()->description('Filter by partial component group name.'),
            'per_page' => $schema->integer()->min(1)->max(100)->default(15),
            'page' => $schema->integer()->min(1)->default(1),
        ];
    }

    public function handle(Request $request): ResponseFactory
    {
        $groups = ComponentGroup::query()
            ->with('components')
            ->when($request->filled('name'), fn ($query) => $query->where('name', 'like', '%'.$request->get('name').'%'))
            ->orderBy('order')
            ->simplePaginate(perPage: $this->perPage($request), page: $this->page($request));

        return Response::structured([
            'data' => $groups->getCollection()->map(fn (ComponentGroup $group) => $this->presentComponentGroup($group))->all(),
            'meta' => $this->paginationMeta($groups),
        ]);
    }
}
