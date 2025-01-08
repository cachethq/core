<?php

namespace Cachet\Data\Requests\Metric;

use Cachet\Data\BaseData;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class CreateMetricPointRequestData extends BaseData
{
    public function __construct(
        public readonly float $value,
        public readonly mixed $timestamp = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return ['value' => ['required', 'numeric']];
    }
}
