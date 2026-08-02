<?php

namespace Cachet\Actions\Metric;

use Cachet\Models\Metric;
use Illuminate\Support\Facades\DB;

class DeleteMetric
{
    /**
     * Handle the action.
     */
    public function handle(Metric $metric): void
    {
        DB::transaction(function () use ($metric): void {
            $metric->metricPoints()->delete();
            $metric->delete();
        });
    }
}
