<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\Metric\CreateMetricPoint;
use Cachet\Actions\Metric\DeleteMetricPoint;
use Cachet\Data\Requests\Metric\CreateMetricPointRequestData;
use Cachet\Http\Resources\MetricPoint as MetricPointResource;
use Cachet\Models\Metric;
use Cachet\Models\MetricPoint;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('Metric Points', weight: 7)]
class MetricPointController extends Controller
{
    /**
     * List Metric Points
     *
     * @queryParam per_page int How many items to show per page. Example: 20
     * @queryParam page int Which page to show. Example: 2
     * @queryParam sort Field to sort by. Enum: name, order, id. Example: name
     * @queryParam include Include related resources. Enum: metric. Example: metric
     */
    public function index(Metric $metric)
    {
        $query = MetricPoint::query()
            ->where('metric_id', $metric->id);

        $points = QueryBuilder::for($query)
            ->allowedIncludes(['metric'])
            ->allowedSorts(['name', 'order', 'id'])
            ->simplePaginate(request('per_page', 15));

        return MetricPointResource::collection($points);
    }

    /**
     * Create Metric Point
     */
    public function store(CreateMetricPointRequestData $data, Metric $metric, CreateMetricPoint $createMetricPointAction)
    {
        $metricPoint = $createMetricPointAction->handle($metric, $data);

        return MetricPointResource::make($metricPoint)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Get Metric Point
     *
     * @queryParam include Include related resources. Enum: metric. Example: metric
     */
    public function show(Metric $metric, MetricPoint $metricPoint)
    {
        $metricPointQuery = QueryBuilder::for($metricPoint)
            ->allowedIncludes(['metric'])
            ->first();

        return MetricPointResource::make($metricPointQuery)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Delete Metric Point
     */
    public function destroy(Metric $metric, MetricPoint $metricPoint, DeleteMetricPoint $deleteMetricPointAction)
    {
        $deleteMetricPointAction->handle($metricPoint);

        return response()->noContent();
    }
}
