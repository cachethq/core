<?php

namespace Cachet\Http\Controllers\Dashboard;

use Cachet\Models\Metric;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Response;

class MetricController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return inertia('Dashboard/Metrics/Index', [
            'metrics' => fn () => Metric::query()->latest()->get(),
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
    public function show(Metric $metric)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Metric $metric)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Metric $metric)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Metric $metric)
    {
        //
    }
}
