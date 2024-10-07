<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\Metric\CreateMetricPoint;
use Cachet\Actions\Metric\DeleteMetricPoint;
use Cachet\Data\Metric\CreateMetricPointData;
use Cachet\Http\Resources\MetricPoint as MetricPointResource;
use Cachet\Models\Metric;
use Cachet\Models\MetricPoint;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\QueryBuilder;

class MetricPointController extends Controller
{
    /**
     * List Metric Points.
     */
    public function index(Metric $metric)
    {
        $points = QueryBuilder::for(MetricPoint::class)
            ->where('metric_id', $metric->id)
            ->allowedIncludes(['metric'])
            ->allowedSorts(['name', 'order', 'id'])
            ->simplePaginate(request('per_page', 15));

        return MetricPointResource::collection($points);
    }

    /**
     * Create Metric Point.
     */
    public function store(CreateMetricPointData $data, Metric $metric, CreateMetricPoint $createMetricPointAction)
    {
        $metricPoint = $createMetricPointAction->handle($metric, $data);

        return MetricPointResource::make($metricPoint)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Get Metric Point.
     */
    public function show(Metric $metric, MetricPoint $metricPoint)
    {
        return MetricPointResource::make($metricPoint)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Delete Metric Point.
     */
    public function destroy(Metric $metric, MetricPoint $metricPoint, DeleteMetricPoint $deleteMetricPointAction)
    {
        $deleteMetricPointAction->handle($metricPoint);

        return response()->noContent();
    }
}
