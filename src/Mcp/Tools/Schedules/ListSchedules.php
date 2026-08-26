<?php

namespace Cachet\Mcp\Tools\Schedules;

use Cachet\Mcp\Concerns\GuardsMcpAbilities;
use Cachet\Mcp\Concerns\InteractsWithPagination;
use Cachet\Mcp\Concerns\PresentsResources;
use Cachet\Models\Schedule;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class ListSchedules extends Tool
{
    use GuardsMcpAbilities;
    use InteractsWithPagination;
    use PresentsResources;

    protected string $name = 'list_schedules';

    protected string $description = 'List maintenance schedules, most recently scheduled first, optionally filtered by name.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()->description('Filter by partial schedule name.'),
            'tags' => $schema->array()->items($schema->string())->description('Return schedules with any of these tags.'),
            'per_page' => $schema->integer()->min(1)->max(100)->default(15),
            'page' => $schema->integer()->min(1)->default(1),
        ];
    }

    public function handle(Request $request): ResponseFactory
    {
        $schedules = Schedule::query()
            ->with('tags')
            ->when(! $this->tokenCan('schedules.manage'), fn ($query) => $query->published())
            ->when($request->filled('name'), fn ($query) => $query->where('name', 'like', '%'.$request->get('name').'%'))
            ->when($request->filled('tags'), fn ($query) => $query->withAnyTags($request->array('tags')))
            ->orderByDesc('scheduled_at')
            ->simplePaginate(perPage: $this->perPage($request), page: $this->page($request));

        return Response::structured([
            'data' => $schedules->getCollection()->map(fn (Schedule $schedule) => $this->presentSchedule($schedule))->all(),
            'meta' => $this->paginationMeta($schedules),
        ]);
    }
}
