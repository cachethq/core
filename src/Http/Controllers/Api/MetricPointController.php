<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\Metric\CreateMetricPoint;
use Cachet\Actions\Metric\DeleteMetricPoint;
use Cachet\Http\Requests\CreateMetricPointRequest;
use Cachet\Http\Resources\MetricPoint as MetricPointResource;
use Cachet\Models\Metric;
use Cachet\Models\MetricPoint;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group Metric Points
 */
class MetricPointController extends Controller
{
    /**
     * List Metric Points
     *
     * @apiResourceCollection \Cachet\Http\Resources\MetricPoint
     *
     * @apiResourceModel \Cachet\Models\MetricPoint
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
     * Create Metric Point
     *
     * @apiResource \Cachet\Http\Resources\MetricPoint
     *
     * @apiResourceModel \Cachet\Models\MetricPoint
     *
     * @authenticated
     */
    public function store(CreateMetricPointRequest $request, Metric $metric, CreateMetricPoint $createMetricPointAction)
    {
        $metricPoint = $createMetricPointAction->handle($metric, $request->validated());

        return MetricPointResource::make($metricPoint)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Get Metric Point
     *
     * @apiResource \Cachet\Http\Resources\MetricPoint
     *
     * @apiResourceModel \Cachet\Models\MetricPoint
     */
    public function show(Metric $metric, MetricPoint $metricPoint)
    {
        return MetricPointResource::make($metricPoint)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Delete Metric Point
     *
     * @response 204
     *
     * @authenticated
     */
    public function destroy(Metric $metric, MetricPoint $metricPoint, DeleteMetricPoint $deleteMetricPointAction)
    {
        $deleteMetricPointAction->handle($metricPoint);

        return response()->noContent();
    }
}
