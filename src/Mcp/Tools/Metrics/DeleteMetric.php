<?php

namespace Cachet\Mcp\Tools\Metrics;

use Cachet\Actions\Metric\DeleteMetric as DeleteMetricAction;
use Cachet\Mcp\Concerns\GuardsMcpAbilities;
use Cachet\Models\Metric;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;

#[IsDestructive]
class DeleteMetric extends Tool
{
    use GuardsMcpAbilities;

    protected string $name = 'delete_metric';

    protected string $description = 'Delete a metric and its points. Requires the metrics.delete token ability.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->required()->description('The metric ID.'),
        ];
    }

    public function handle(Request $request, DeleteMetricAction $action): Response
    {
        if (! $this->tokenCan('metrics.delete')) {
            return $this->missingAbility('metrics.delete');
        }

        $metric = Metric::query()->find($id = $request->integer('id'));

        if ($metric === null) {
            return Response::error("Metric [{$id}] not found.");
        }

        $action->handle($metric);

        return Response::text("Metric [{$id}] deleted.");
    }

    public function shouldRegister(): bool
    {
        return $this->tokenCan('metrics.delete');
    }
}
