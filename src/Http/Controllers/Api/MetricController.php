<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\Metric\CreateMetric;
use Cachet\Actions\Metric\DeleteMetric;
use Cachet\Actions\Metric\UpdateMetric;
use Cachet\Http\Requests\CreateMetricRequest;
use Cachet\Http\Requests\UpdateMetricRequest;
use Cachet\Http\Resources\Metric as MetricResource;
use Cachet\Models\Metric;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\QueryBuilder;

class MetricController extends Controller
{
    /**
     * List Metrics.
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
     * Create Metric.
     */
    public function store(CreateMetricRequest $request, CreateMetric $createMetricAction)
    {
        $metric = $createMetricAction->handle($request->validated());

        return MetricResource::make($metric);
    }

    /**
     * Get Metric.
     */
    public function show(Metric $metric)
    {
        return MetricResource::make($metric)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update Metric.
     */
    public function update(UpdateMetricRequest $request, Metric $metric, UpdateMetric $updateMetricAction)
    {
        $updateMetricAction->handle($metric, $request->validated());

        return MetricResource::make($metric->fresh());
    }

    /**
     * Delete Metric.
     */
    public function destroy(Metric $metric, DeleteMetric $deleteMetricAction)
    {
        $deleteMetricAction->handle($metric);

        return response()->noContent();
    }
}
