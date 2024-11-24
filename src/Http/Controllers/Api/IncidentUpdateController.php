<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\IncidentUpdate\CreateIncidentUpdate;
use Cachet\Actions\IncidentUpdate\DeleteIncidentUpdate;
use Cachet\Actions\IncidentUpdate\UpdateIncidentUpdate;
use Cachet\Http\Requests\CreateIncidentUpdateRequest;
use Cachet\Http\Requests\UpdateIncidentUpdateRequest;
use Cachet\Http\Resources\IncidentUpdate as IncidentUpdateResource;
use Cachet\Models\Incident;
use Cachet\Models\IncidentUpdate;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group Incident Updates
 */
class IncidentUpdateController extends Controller
{
    /**
     * List Incident Updates
     *
     * @apiResourceCollection \Cachet\Http\Resources\IncidentUpdate
     *
     * @apiResourceModel \Cachet\Models\IncidentUpdate
     */
    public function index(Incident $incident)
    {
        $updates = QueryBuilder::for(IncidentUpdate::class)
            ->where('incident_id', $incident->id)
            ->allowedFilters(['status'])
            ->allowedSorts(['status', 'created_at'])
            ->simplePaginate(request('per_page', 15));

        return IncidentUpdateResource::collection($updates);
    }

    /**
     * Create Incident Update
     *
     * @apiResource \Cachet\Http\Resources\IncidentUpdate
     *
     * @apiResourceModel \Cachet\Models\IncidentUpdate
     *
     * @authenticated
     */
    public function store(CreateIncidentUpdateRequest $request, Incident $incident, CreateIncidentUpdate $createIncidentUpdateAction)
    {
        $incidentUpdate = $createIncidentUpdateAction->handle($incident, $request->validated());

        return IncidentUpdateResource::make($incidentUpdate);
    }

    /**
     * Get Incident Update
     *
     * @apiResource \Cachet\Http\Resources\IncidentUpdate
     *
     * @apiResourceModel \Cachet\Models\IncidentUpdate
     */
    public function show(Incident $incident, IncidentUpdate $incidentUpdate)
    {
        return IncidentUpdateResource::make($incidentUpdate)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update Incident Update
     *
     * @apiResource \Cachet\Http\Resources\IncidentUpdate
     *
     * @apiResourceModel \Cachet\Models\IncidentUpdate
     *
     * @authenticated
     */
    public function update(UpdateIncidentUpdateRequest $request, Incident $incident, IncidentUpdate $incidentUpdate, UpdateIncidentUpdate $updateIncidentUpdateAction)
    {
        $updateIncidentUpdateAction->handle($incidentUpdate, $request->validated());

        return IncidentUpdateResource::make($incidentUpdate->fresh());
    }

    /**
     * Delete Incident Update
     *
     * @response 204
     *
     * @authenticated
     */
    public function destroy(Incident $incident, IncidentUpdate $incidentUpdate, DeleteIncidentUpdate $deleteIncidentUpdateAction)
    {
        $deleteIncidentUpdateAction->handle($incidentUpdate);

        return response()->noContent();
    }
}
