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
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
     */
    public function store(CreateMetricRequest $request)
    {
        $metric = CreateMetric::run($request->validated());

        return MetricResource::make($metric);
    }

    /**
     * Display the specified resource.
     */
    public function show(Metric $metric)
    {
        $metric = QueryBuilder::for($metric)
            ->allowedIncludes(['points'])
            ->first();

        return MetricResource::make($metric)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMetricRequest $request, Metric $metric)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Metric $metric)
    {
        //
    }
}
