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

class IncidentUpdateController extends Controller
{
    /**
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
     */
    public function store(CreateIncidentUpdateRequest $request, Incident $incident)
    {
        $incidentUpdate = CreateIncidentUpdate::run($incident, $request->validated());

        return IncidentUpdateResource::make($incidentUpdate);
    }

    /**
     * Display the specified resource.
     */
    public function show(Incident $incident, IncidentUpdate $incidentUpdate)
    {
        return IncidentUpdateResource::make($incidentUpdate)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIncidentUpdateRequest $request, Incident $incident, IncidentUpdate $incidentUpdate)
    {
        UpdateIncidentUpdate::run($incidentUpdate, $request->validated());

        return IncidentUpdateResource::make($incidentUpdate->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Incident $incident, IncidentUpdate $incidentUpdate)
    {
        DeleteIncidentUpdate::run($incidentUpdate);

        return response()->noContent();
    }
}
