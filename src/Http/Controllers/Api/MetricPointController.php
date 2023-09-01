<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Models\Metric;
use Cachet\Models\MetricPoint;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MetricPointController extends Controller
{
    /**
     * List Metric Points.
     */
    public function index(Metric $metric)
    {
        //
    }

    /**
     * Create Metric Point.
     */
    public function store(Request $request, Metric $metric)
    {
        //
    }

    /**
     * Get Metric Point.
     */
    public function show(Metric $metric, MetricPoint $metricPoint)
    {
        //
    }

    /**
     * Delete Metric Point.
     */
    public function destroy(Metric $metric, MetricPoint $metricPoint)
    {
        //
    }
}
