<?php

namespace Cachet\Mcp\Tools\MetricPoints;

use Cachet\Actions\Metric\DeleteMetricPoint as DeleteMetricPointAction;
use Cachet\Mcp\Concerns\GuardsMcpAbilities;
use Cachet\Models\Metric;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;

#[IsDestructive]
class DeleteMetricPoint extends Tool
{
    use GuardsMcpAbilities;

    protected string $name = 'delete_metric_point';

    protected string $description = 'Delete a point from a metric. Requires the metric-points.delete token ability.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'metric_id' => $schema->integer()->required()->description('The metric ID.'),
            'metric_point_id' => $schema->integer()->required()->description('The metric point ID.'),
        ];
    }

    public function handle(Request $request, DeleteMetricPointAction $action): Response
    {
        if (! $this->tokenCan('metric-points.delete')) {
            return $this->missingAbility('metric-points.delete');
        }

        $metric = Metric::query()->find($metricId = $request->integer('metric_id'));

        if ($metric === null) {
            return Response::error("Metric [{$metricId}] not found.");
        }

        $point = $metric->metricPoints()->find($pointId = $request->integer('metric_point_id'));

        if ($point === null) {
            return Response::error("Metric point [{$pointId}] not found on metric [{$metricId}].");
        }

        $action->handle($point);

        return Response::text("Metric point [{$pointId}] deleted from metric [{$metricId}].");
    }

    public function shouldRegister(): bool
    {
        return $this->tokenCan('metric-points.delete');
    }
}
