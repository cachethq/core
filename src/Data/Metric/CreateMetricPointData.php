<?php

namespace Cachet\Data\Metric;

use Cachet\Data\BaseData;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;

final class CreateMetricPointData extends BaseData
{
    public function __construct(
        #[Required, Numeric]
        public readonly float $value,
        public readonly mixed $timestamp = null,
    ) {}
}
