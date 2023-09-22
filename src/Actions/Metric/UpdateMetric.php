<?php

declare(strict_types=1);

namespace Cachet\Actions\Metric;

use Cachet\Models\Metric;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateMetric
{
    use AsAction;

    public function handle(Metric $metric, ?array $data = []): Metric
    {
        $metric->update($data);

        return $metric->fresh();
    }
}
