<?php

namespace Cachet\Mcp\Tools\MetricPoints;

use Cachet\Actions\Metric\CreateMetricPoint;
use Cachet\Data\Requests\Metric\CreateMetricPointRequestData;
use Cachet\Mcp\Concerns\GuardsMcpAbilities;
use Cachet\Mcp\Concerns\PresentsResources;
use Cachet\Models\Metric;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;

class AddMetricPoint extends Tool
{
    use GuardsMcpAbilities;
    use PresentsResources;

    protected string $name = 'add_metric_point';

    protected string $description = 'Record a new point on a metric. Requires the metric-points.manage token ability.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'metric_id' => $schema->integer()->required()->description('The metric ID.'),
            'value' => $schema->number()->required()->description('The value to record.'),
            'timestamp' => $schema->string()->description('When the value was recorded, as an ISO-8601 datetime or Unix timestamp. Defaults to now.'),
        ];
    }

    public function handle(Request $request, CreateMetricPoint $action): Response|ResponseFactory
    {
        if (! $this->tokenCan('metric-points.manage')) {
            return $this->missingAbility('metric-points.manage');
        }

        $metric = Metric::query()->find($id = $request->integer('metric_id'));

        if ($metric === null) {
            return Response::error("Metric [{$id}] not found.");
        }

        $data = CreateMetricPointRequestData::validateAndCreate($request->only(['value', 'timestamp']));

        return Response::structured(['data' => $this->presentMetricPoint($action->handle($metric, $data))]);
    }

    public function shouldRegister(): bool
    {
        return $this->tokenCan('metric-points.manage');
    }
}
