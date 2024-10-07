<?php

namespace Cachet\Data\Metric;

use Cachet\Data\BaseData;
use Cachet\Rules\FactorOfSixty;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;

final class CreateMetricData extends BaseData
{
    public function __construct(
        #[Required, Max(255)]
        public readonly string $name,
        #[Required, Max(255)]
        public readonly string $suffix,
        public readonly ?string $description = null,
        public readonly ?float $defaultValue = null,
        #[Min(0), Max(60), Rule(new FactorOfSixty())]
        public readonly ?int $threshold = null,
    ) {}
}
