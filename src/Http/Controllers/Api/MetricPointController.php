<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\Metric\CreateMetricPoint;
use Cachet\Actions\Metric\DeleteMetricPoint;
use Cachet\Concerns\GuardsApiAbilities;
use Cachet\Data\Requests\Metric\CreateMetricPointRequestData;
use Cachet\Http\Resources\MetricPoint as MetricPointResource;
use Cachet\Models\Metric;
use Cachet\Models\MetricPoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('Metric Points', weight: 7)]
class MetricPointController extends Controller
{
    use GuardsApiAbilities;

    /**
     * List Metric Points
     */
    #[QueryParameter('per_page', 'How many items to show per page.', type: 'int', default: 15, example: 20)]
    #[QueryParameter('page', 'Which page to show.', type: 'int', example: 2)]
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
        $this->guard('metric-points.manage');

        $metricPoint = $createMetricPointAction->handle($metric, $data);

        return MetricPointResource::make($metricPoint)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Get Metric Point
     */
    public function show(Metric $metric, MetricPoint $metricPoint)
    {

        $metricPointQuery = QueryBuilder::for(MetricPoint::class)
            ->allowedIncludes(['metric'])
            ->find($metricPoint->id);

        return MetricPointResource::make($metricPointQuery)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Delete Metric Point
     */
    public function destroy(Metric $metric, MetricPoint $metricPoint, DeleteMetricPoint $deleteMetricPointAction)
    {
        $this->guard('metric-points.delete');

        $deleteMetricPointAction->handle($metricPoint);

        return response()->noContent();
    }
}
