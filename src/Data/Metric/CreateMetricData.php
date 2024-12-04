<?php

namespace Cachet\Data\Metric;

use Cachet\Data\BaseData;
use Cachet\Enums\MetricTypeEnum;
use Cachet\Rules\FactorOfSixty;
use Spatie\LaravelData\Attributes\Validation\Enum;
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
        #[Enum(MetricTypeEnum::class)]
        public readonly ?MetricTypeEnum $calcType = null,
        public readonly ?string $description = null,
        public readonly ?float $defaultValue = null,
        public readonly ?bool $displayChart = null,
        #[Min(0), Max(60), Rule(new FactorOfSixty())]
        public readonly ?int $threshold = null,
        public readonly ?int $places = null,
    ) {}
}
