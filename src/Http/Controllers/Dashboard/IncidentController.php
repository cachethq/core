<?php

declare(strict_types=1);

namespace Cachet\Http\Controllers\Dashboard;

use Cachet\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class IncidentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('Dashboard/Incidents/Index', [
            'incidents' => fn () => Incident::query()->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Incident $incident)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Incident $incident)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Incident $incident)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Incident $incident)
    {
        //
    }
}
