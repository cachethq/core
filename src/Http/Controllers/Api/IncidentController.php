<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\Incident\CreateIncident;
use Cachet\Actions\Incident\DeleteIncident;
use Cachet\Actions\Incident\UpdateIncident;
use Cachet\Http\Requests\CreateIncidentRequest;
use Cachet\Http\Requests\UpdateIncidentRequest;
use Cachet\Http\Resources\Incident as IncidentResource;
use Cachet\Models\Incident;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\QueryBuilder;

class IncidentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $incidents = QueryBuilder::for(Incident::class)
            ->allowedIncludes(['updates'])
            ->allowedFilters(['name', 'status', 'occurred_at'])
            ->allowedSorts(['name', 'status', 'id'])
            ->simplePaginate(request('per_page', 15));

        return IncidentResource::collection($incidents);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateIncidentRequest $request)
    {
        $incident = CreateIncident::run($request->validated());

        return IncidentResource::make($incident);
    }

    /**
     * Display the specified resource.
     */
    public function show(Incident $incident)
    {
        return IncidentResource::make($incident)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIncidentRequest $request, Incident $incident)
    {
        UpdateIncident::run($incident, $request->validated());

        return IncidentResource::make($incident->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Incident $incident)
    {
        DeleteIncident::run($incident);

        return response()->noContent();
    }
}
