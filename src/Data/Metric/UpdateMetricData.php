<?php

namespace Cachet\Data\Metric;

use Cachet\Data\BaseData;
use Cachet\Rules\FactorOfSixty;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Rule;

final class UpdateMetricData extends BaseData
{
    public function __construct(
        #[Max(255)]
        public readonly string $name,
        #[Max(255)]
        public readonly ?string $suffix = null,
        public readonly ?string $description = null,
        public readonly ?float $defaultValue = null,
        #[Min(0), Max(60), Rule(new FactorOfSixty)]
        public readonly ?int $threshold = null,
    ) {}
}
