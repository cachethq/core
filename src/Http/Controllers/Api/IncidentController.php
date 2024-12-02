<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\Incident\CreateIncident;
use Cachet\Actions\Incident\DeleteIncident;
use Cachet\Actions\Incident\UpdateIncident;
use Cachet\Http\Requests\CreateIncidentRequest;
use Cachet\Http\Requests\UpdateIncidentRequest;
use Cachet\Http\Resources\Incident as IncidentResource;
use Cachet\Models\Incident;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group Incidents
 */
class IncidentController extends Controller
{
    /**
     * The list of allowed includes.
     */
    public const ALLOWED_INCLUDES = [
        'components',
        'incidentUpdates',
        'user',
    ];

    /**
     * List Incidents
     *
     * @apiResourceCollection \Cachet\Http\Resources\Incident
     *
     * @apiResourceModel \Cachet\Models\Incident
     *
     * @queryParam per_page int How many items to show per page. Example: 20
     * @queryParam page int Which page to show. Example: 2
     * @queryParam sort string Field to sort by. Enum: name, id, status Example: status
     * @queryParam include string Include related resources. Enum: components, incidentUpdates, user Example: incidentUpdates
     */
    public function index()
    {
        $incidents = QueryBuilder::for(Incident::class)
            ->when(! request('sort'), function (Builder $builder) {
                $builder->orderByDesc('created_at');
            })
            ->allowedIncludes(self::ALLOWED_INCLUDES)
            ->allowedFilters(['name', 'status', 'occurred_at'])
            ->allowedSorts(['name', 'status', 'id'])
            ->simplePaginate(request('per_page', 15));

        return IncidentResource::collection($incidents);
    }

    /**
     * Create Incident
     *
     * @apiResource \Cachet\Http\Resources\Incident
     *
     * @apiResourceModel \Cachet\Models\Incident
     *
     * @authenticated
     */
    public function store(CreateIncidentRequest $request, CreateIncident $createIncidentAction)
    {
        $incident = $createIncidentAction->handle($request->validated());

        return IncidentResource::make($incident);
    }

    /**
     * Get Incident
     *
     * @apiResource \Cachet\Http\Resources\Incident
     *
     * @apiResourceModel \Cachet\Models\Incident
     *
     * @queryParam include string Include related resources. Enum: components, incidentUpdates, user Example: incidentUpdates
     */
    public function show(Incident $incident)
    {
        $incidentQuery = QueryBuilder::for($incident)
            ->allowedIncludes(self::ALLOWED_INCLUDES)
            ->first();

        return IncidentResource::make($incidentQuery)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update Incident
     *
     * @apiResource \Cachet\Http\Resources\Incident
     *
     * @apiResourceModel \Cachet\Models\Incident
     *
     * @authenticated
     */
    public function update(UpdateIncidentRequest $request, Incident $incident, UpdateIncident $updateIncidentAction)
    {
        $updateIncidentAction->handle($incident, $request->validated());

        return IncidentResource::make($incident->fresh());
    }

    /**
     * Delete Incident
     *
     * @response 204
     *
     * @authenticated
     */
    public function destroy(Incident $incident, DeleteIncident $deleteIncidentAction)
    {
        $deleteIncidentAction->handle($incident);

        return response()->noContent();
    }
}
