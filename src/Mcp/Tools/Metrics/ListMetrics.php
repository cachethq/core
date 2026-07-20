<?php

namespace Cachet\Mcp\Tools\Metrics;

use Cachet\Concerns\ChecksApiAuthentication;
use Cachet\Mcp\Concerns\InteractsWithPagination;
use Cachet\Mcp\Concerns\PresentsResources;
use Cachet\Models\Metric;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class ListMetrics extends Tool
{
    use ChecksApiAuthentication;
    use InteractsWithPagination;
    use PresentsResources;

    protected string $name = 'list_metrics';

    protected string $description = 'List metrics on the status page, ordered by their configured order, optionally filtered by name.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()->description('Filter by partial metric name.'),
            'per_page' => $schema->integer()->min(1)->max(100)->default(15),
            'page' => $schema->integer()->min(1)->default(1),
        ];
    }

    public function handle(Request $request): ResponseFactory
    {
        $metrics = Metric::query()
            ->visible($this->isAuthenticated())
            ->when($request->filled('name'), fn ($query) => $query->where('name', 'like', '%'.$request->get('name').'%'))
            ->orderBy('order')
            ->simplePaginate(perPage: $this->perPage($request), page: $this->page($request));

        return Response::structured([
            'data' => $metrics->getCollection()->map(fn (Metric $metric) => $this->presentMetric($metric))->all(),
            'meta' => $this->paginationMeta($metrics),
        ]);
    }
}
