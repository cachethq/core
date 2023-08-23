<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Models\Metric;
use Cachet\Models\MetricPoint;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MetricPointController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Metric $metric)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Metric $metric)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Metric $metric, MetricPoint $metricPoint)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Metric $metric, MetricPoint $metricPoint)
    {
        //
    }
}
