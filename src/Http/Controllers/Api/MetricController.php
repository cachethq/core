<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\Metric\CreateMetric;
use Cachet\Actions\Metric\DeleteMetric;
use Cachet\Actions\Metric\UpdateMetric;
use Cachet\Data\Requests\Metric\CreateMetricRequestData;
use Cachet\Data\Requests\Metric\UpdateMetricRequestData;
use Cachet\Http\Resources\Metric as MetricResource;
use Cachet\Models\Metric;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('Metrics', weight: 6)]
class MetricController extends Controller
{
    /**
     * List Metrics
     *
     * @queryParam per_page int How many items to show per page. Example: 20
     * @queryParam page int Which page to show. Example: 2
     * @queryParam sort Field to sort by. Enum: name, order, id. Example: name
     * @queryParam include Include related resources. Enum: points. Example: points
     * @queryParam filters[name] string Filter by name. Example: metric name
     * @queryParam filters[calc_type] Enum:Cachet\Enums\MetricTypeEnum Filter by calculation type. Example: sum,avg
     */
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
        $metric = $createMetricAction->handle($data);

        return MetricResource::make($metric);
    }

    /**
     * Get Metric
     *
     * @queryParam include Include related resources. Enum: points. Example: points
     */
    public function show(Metric $metric)
    {
        $metricQuery = QueryBuilder::for($metric)
            ->allowedIncludes(['points'])
            ->first();

        return MetricResource::make($metricQuery)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update Metric
     */
    public function update(UpdateMetricRequestData $data, Metric $metric, UpdateMetric $updateMetricAction)
    {
        $updateMetricAction->handle($metric, $data);

        return MetricResource::make($metric->fresh());
    }

    /**
     * Delete Metric
     */
    public function destroy(Metric $metric, DeleteMetric $deleteMetricAction)
    {
        $deleteMetricAction->handle($metric);

        return response()->noContent();
    }
}
