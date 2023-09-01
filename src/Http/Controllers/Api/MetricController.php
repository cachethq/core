<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\Metric\CreateMetric;
use Cachet\Http\Requests\CreateMetricRequest;
use Cachet\Http\Requests\UpdateMetricRequest;
use Cachet\Http\Resources\Metric as MetricResource;
use Cachet\Models\Metric;
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
            ->allowedIncludes(['points'])
            ->allowedFilters(['name', 'calc_type'])
            ->allowedSorts(['name', 'order', 'id'])
            ->simplePaginate(request('per_page', 15));

        return MetricResource::collection($metrics);
    }

    /**
     * Create Metric.
     */
    public function store(CreateMetricRequest $request)
    {
        $metric = CreateMetric::run($request->validated());

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
    public function update(UpdateMetricRequest $request, Metric $metric)
    {
        //
    }

    /**
     * Delete Metric.
     */
    public function destroy(Metric $metric)
    {
        //
    }
}
