<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\Metric\CreateMetric;
use Cachet\Actions\Metric\DeleteMetric;
use Cachet\Actions\Metric\UpdateMetric;
use Cachet\Data\Metric\CreateMetricData;
use Cachet\Data\Metric\UpdateMetricData;
use Cachet\Http\Resources\Metric as MetricResource;
use Cachet\Models\Metric;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group Metrics
 */
class MetricController extends Controller
{
    /**
     * List Metrics
     *
     * @apiResourceCollection \Cachet\Http\Resources\Metric
     *
     * @apiResourceModel \Cachet\Models\Metric
     *
     * @queryParam per_page int How many items to show per page. Example: 20
     * @queryParam page int Which page to show. Example: 2
     * @queryParam sort string Field to sort by. Enum: name, order, id enabled Example: name
     * @queryParam include string Include related resources. Enum: points Example: points
     * @queryParam filters string[] Filter the resources. Example: name=api
     */
    public function index()
    {
        $metrics = QueryBuilder::for(Metric::class)
            ->when(! request('sort'), function (Builder $builder) {
                $builder->orderByDesc('created_at');
            })
            ->allowedIncludes(['points'])
            ->allowedFilters(['name', 'calc_type'])
            ->allowedSorts(['name', 'order', 'id'])
            ->simplePaginate(request('per_page', 15));

        return MetricResource::collection($metrics);
    }

    /**
     * Create Metric
     *
     * @apiResource \Cachet\Http\Resources\Metric
     *
     * @apiResourceModel \Cachet\Models\Metric
     *
     * @authenticated
     */
    public function store(CreateMetricData $data, CreateMetric $createMetricAction)
    {
        $metric = $createMetricAction->handle($data);

        return MetricResource::make($metric);
    }

    /**
     * Get Metric
     *
     * @apiResource \Cachet\Http\Resources\Metric
     *
     * @apiResourceModel \Cachet\Models\Metric
     */
    public function show(Metric $metric)
    {
        return MetricResource::make($metric)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update Metric
     *
     * @apiResource \Cachet\Http\Resources\Metric
     *
     * @apiResourceModel \Cachet\Models\Metric
     *
     * @authenticated
     */
    public function update(UpdateMetricData $data, Metric $metric, UpdateMetric $updateMetricAction)
    {
        $updateMetricAction->handle($metric, $data);

        return MetricResource::make($metric->fresh());
    }

    /**
     * Delete Metric
     *
     * @response 204
     *
     * @authenticated
     */
    public function destroy(Metric $metric, DeleteMetric $deleteMetricAction)
    {
        $deleteMetricAction->handle($metric);

        return response()->noContent();
    }
}
