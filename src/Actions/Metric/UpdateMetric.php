<?php

namespace Cachet\Actions\Metric;

use Cachet\Data\Requests\Metric\UpdateMetricRequestData;
use Cachet\Models\Metric;

class UpdateMetric
{
    /**
     * Handle the action.
     */
    public function handle(Metric $metric, UpdateMetricRequestData $data): Metric
    {
        $metric->update($data->except('tags')->toArray());

        if ($data->tags !== null) {
            $metric->syncTags($data->tags);
        }

        return $metric->fresh();
    }
}
