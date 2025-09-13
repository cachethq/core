<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\Metric\CreateMetric;
use Cachet\Actions\Metric\DeleteMetric;
use Cachet\Actions\Metric\UpdateMetric;
use Cachet\Concerns\GuardsApiAbilities;
use Cachet\Data\Requests\Metric\CreateMetricRequestData;
use Cachet\Data\Requests\Metric\UpdateMetricRequestData;
use Cachet\Enums\MetricTypeEnum;
use Cachet\Http\Resources\Metric as MetricResource;
use Cachet\Models\Metric;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('Metrics', weight: 6)]
class MetricController extends Controller
{
    use GuardsApiAbilities;

    /**
     * List Metrics
     */
    #[QueryParameter('filter[name]', 'Filter by name.', example: 'metric name')]
    #[QueryParameter('filter[calc_type]', 'Filter by calculation type.', type: MetricTypeEnum::class)]
    #[QueryParameter('per_page', 'How many items to show per page.', type: 'int', default: 15, example: 20)]
    #[QueryParameter('page', 'Which page to show.', type: 'int', example: 2)]
    public function index()
    {
        $query = Metric::query()
            ->when(! request('sort'), function (Builder $builder) {
                $builder->orderByDesc('created_at');
            });

        $metrics = QueryBuilder::for($query)
            ->allowedIncludes(['points'])
            ->allowedFilters(['name', 'calc_type'])
            ->allowedSorts(['name', 'order', 'id'])
            ->simplePaginate(request('per_page', 15));

        return MetricResource::collection($metrics);
    }

    /**
     * Create Metric
     */
    public function store(CreateMetricRequestData $data, CreateMetric $createMetricAction)
    {
        $this->guard('metrics.manage');

        $metric = $createMetricAction->handle($data);

        return MetricResource::make($metric);
    }

    /**
     * Get Metric
     */
    public function show(Metric $metric)
    {
        $metricQuery = QueryBuilder::for(Metric::class)
            ->allowedIncludes(['points'])
            ->find($metric->id);

        return MetricResource::make($metricQuery)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update Metric
     */
    public function update(UpdateMetricRequestData $data, Metric $metric, UpdateMetric $updateMetricAction)
    {
        $this->guard('metrics.manage');

        $updateMetricAction->handle($metric, $data);

        return MetricResource::make($metric->fresh());
    }

    /**
     * Delete Metric
     */
    public function destroy(Metric $metric, DeleteMetric $deleteMetricAction)
    {
        $this->guard('metrics.delete');

        $deleteMetricAction->handle($metric);

        return response()->noContent();
    }
}
