<?php

namespace Cachet\Mcp\Tools\Metrics;

use Cachet\Concerns\ChecksApiAuthentication;
use Cachet\Mcp\Concerns\PresentsResources;
use Cachet\Models\Metric;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class GetMetric extends Tool
{
    use ChecksApiAuthentication;
    use PresentsResources;

    protected string $name = 'get_metric';

    protected string $description = 'Get a single metric by ID, including its most recent metric points.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->required()->description('The metric ID.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        $metric = Metric::query()
            ->visible($this->isAuthenticated())
            ->with(['metricPoints' => fn ($query) => $query->latest()->limit(50)])
            ->find($id = $request->integer('id'));

        if ($metric === null) {
            return Response::error("Metric [{$id}] not found.");
        }

        return Response::structured(['data' => $this->presentMetric($metric)]);
    }
}
