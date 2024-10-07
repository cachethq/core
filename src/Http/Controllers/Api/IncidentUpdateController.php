<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\IncidentUpdate\CreateIncidentUpdate;
use Cachet\Actions\IncidentUpdate\DeleteIncidentUpdate;
use Cachet\Actions\IncidentUpdate\UpdateIncidentUpdate;
use Cachet\Data\IncidentUpdate\CreateIncidentUpdateData;
use Cachet\Data\IncidentUpdate\UpdateIncidentUpdateData;
use Cachet\Http\Resources\IncidentUpdate as IncidentUpdateResource;
use Cachet\Models\Incident;
use Cachet\Models\IncidentUpdate;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\QueryBuilder;

class IncidentUpdateController extends Controller
{
    /**
     * List Incident Updates.
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
     * Create Incident Update.
     */
    public function store(CreateIncidentUpdateData $data, Incident $incident, CreateIncidentUpdate $createIncidentUpdateAction)
    {
        $incidentUpdate = $createIncidentUpdateAction->handle($incident, $data);

        return IncidentUpdateResource::make($incidentUpdate);
    }

    /**
     * Get Incident Update.
     */
    public function show(Incident $incident, IncidentUpdate $incidentUpdate)
    {
        return IncidentUpdateResource::make($incidentUpdate)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update Incident Update.
     */
    public function update(UpdateIncidentUpdateData $data, Incident $incident, IncidentUpdate $incidentUpdate, UpdateIncidentUpdate $updateIncidentUpdateAction)
    {
        $updateIncidentUpdateAction->handle($incidentUpdate, $data);

        return IncidentUpdateResource::make($incidentUpdate->fresh());
    }

    /**
     * Delete Incident Update.
     */
    public function destroy(Incident $incident, IncidentUpdate $incidentUpdate, DeleteIncidentUpdate $deleteIncidentUpdateAction)
    {
        $deleteIncidentUpdateAction->handle($incidentUpdate);

        return response()->noContent();
    }
}
