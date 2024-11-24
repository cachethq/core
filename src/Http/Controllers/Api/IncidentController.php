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
     * List Incidents
     *
     * @apiResourceCollection \Cachet\Http\Resources\Incident
     * @apiResourceModel \Cachet\Models\Incident
     */
    public function index()
    {
        $incidents = QueryBuilder::for(Incident::class)
            ->when(! request('sort'), function (Builder $builder) {
                $builder->orderByDesc('created_at');
            })
            ->allowedIncludes(['updates'])
            ->allowedFilters(['name', 'status', 'occurred_at'])
            ->allowedSorts(['name', 'status', 'id'])
            ->simplePaginate(request('per_page', 15));

        return IncidentResource::collection($incidents);
    }

    /**
     * Create Incident
     *
     * @apiResource \Cachet\Http\Resources\Incident
     * @apiResourceModel \Cachet\Models\Incident
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
     * @apiResourceModel \Cachet\Models\Incident
     */
    public function show(Incident $incident)
    {
        return IncidentResource::make($incident)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update Incident
     *
     * @apiResource \Cachet\Http\Resources\Incident
     * @apiResourceModel \Cachet\Models\Incident
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
     * @authenticated
     */
    public function destroy(Incident $incident, DeleteIncident $deleteIncidentAction)
    {
        $deleteIncidentAction->handle($incident);

        return response()->noContent();
    }
}
