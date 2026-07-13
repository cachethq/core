<?php

namespace Cachet\Mcp\Tools\Metrics;

use Cachet\Actions\Metric\UpdateMetric as UpdateMetricAction;
use Cachet\Data\Requests\Metric\UpdateMetricRequestData;
use Cachet\Mcp\Concerns\GuardsMcpAbilities;
use Cachet\Mcp\Concerns\PresentsResources;
use Cachet\Models\Metric;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;

#[IsIdempotent]
class UpdateMetric extends Tool
{
    use GuardsMcpAbilities;
    use PresentsResources;

    protected string $name = 'update_metric';

    protected string $description = 'Update an existing metric. Requires the metrics.manage token ability.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->required()->description('The metric ID.'),
            'name' => $schema->string()->max(255)->description('The name of the metric.'),
            'suffix' => $schema->string()->max(255)->description('The suffix shown after the metric value, such as ms or %.'),
            'description' => $schema->string()->description('A description of the metric.'),
            'default_value' => $schema->number()->description('The default value applied when no points are recorded.'),
            'threshold' => $schema->integer()->min(0)->max(60)->description('The number of minutes between plotted points. Must be a factor of sixty.'),
        ];
    }

    public function handle(Request $request, UpdateMetricAction $action): Response|ResponseFactory
    {
        if (! $this->tokenCan('metrics.manage')) {
            return $this->missingAbility('metrics.manage');
        }

        $metric = Metric::query()->find($id = $request->integer('id'));

        if ($metric === null) {
            return Response::error("Metric [{$id}] not found.");
        }

        $data = UpdateMetricRequestData::validateAndCreate(
            $request->only(['name', 'suffix', 'description', 'default_value', 'threshold'])
        );

        return Response::structured(['data' => $this->presentMetric($action->handle($metric, $data))]);
    }

    public function shouldRegister(): bool
    {
        return $this->tokenCan('metrics.manage');
    }
}
