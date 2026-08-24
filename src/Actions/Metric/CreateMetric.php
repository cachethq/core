<?php

namespace Cachet\Actions\Metric;

use Cachet\Data\Requests\Metric\CreateMetricRequestData;
use Cachet\Models\Metric;

class CreateMetric
{
    /**
     * Handle the action.
     */
    public function handle(CreateMetricRequestData $data): Metric
    {
        return tap(Metric::create($data->except('tags')->toArray()), fn (Metric $metric) => $metric->syncTags($data->tags));
    }
}
