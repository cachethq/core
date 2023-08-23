<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Models\Incident;
use Cachet\Models\IncidentUpdate;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class IncidentUpdateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Incident $incident)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Incident $incident)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Incident $incident, IncidentUpdate $incidentUpdate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Incident $incident, IncidentUpdate $incidentUpdate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Incident $incident, IncidentUpdate $incidentUpdate)
    {
        //
    }
}
