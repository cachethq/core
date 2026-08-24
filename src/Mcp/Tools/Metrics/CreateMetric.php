<?php

namespace Cachet\Mcp\Tools\Metrics;

use Cachet\Actions\Metric\CreateMetric as CreateMetricAction;
use Cachet\Data\Requests\Metric\CreateMetricRequestData;
use Cachet\Enums\MetricTypeEnum;
use Cachet\Mcp\Concerns\GuardsMcpAbilities;
use Cachet\Mcp\Concerns\PresentsResources;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;

class CreateMetric extends Tool
{
    use GuardsMcpAbilities;
    use PresentsResources;

    protected string $name = 'create_metric';

    protected string $description = 'Create a new metric. Requires the metrics.manage token ability.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()->max(255)->required()->description('The name of the metric.'),
            'suffix' => $schema->string()->max(255)->required()->description('The suffix shown after the metric value, such as ms or %.'),
            'description' => $schema->string()->description('A description of the metric.'),
            'calc_type' => $schema->integer()
                ->enum(array_column(MetricTypeEnum::cases(), 'value'))
                ->description('How points are aggregated: 0 sum, 1 average.'),
            'default_value' => $schema->number()->description('The default value applied when no points are recorded.'),
            'display_chart' => $schema->boolean()->description('Whether to render a chart for the metric on the status page.'),
            'threshold' => $schema->integer()->min(0)->max(60)->description('The number of minutes between plotted points. Must be a factor of sixty.'),
            'places' => $schema->integer()->description('The number of decimal places shown for values.'),
            'tags' => $schema->array()->items($schema->string())->description('Tags to apply to the metric.'),
        ];
    }

    public function handle(Request $request, CreateMetricAction $action): Response|ResponseFactory
    {
        if (! $this->tokenCan('metrics.manage')) {
            return $this->missingAbility('metrics.manage');
        }

        $data = CreateMetricRequestData::validateAndCreate($request->all());

        return Response::structured(['data' => $this->presentMetric($action->handle($data))]);
    }

    public function shouldRegister(): bool
    {
        return $this->tokenCan('metrics.manage');
    }
}
