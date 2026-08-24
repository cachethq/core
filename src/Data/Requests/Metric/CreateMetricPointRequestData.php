<?php

namespace Cachet\Data\Requests\Metric;

use Cachet\Data\BaseData;
use Cachet\Data\Casts\FlexibleDateTimeCast;
use Cachet\Rules\ValidTimestamp;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class CreateMetricPointRequestData extends BaseData
{
    public function __construct(
        public readonly float $value,
        #[WithCast(FlexibleDateTimeCast::class)]
        public readonly ?Carbon $timestamp = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'value' => ['required', 'numeric'],
            /**
             * The date/time or Unix timestamp the metric point was recorded at. Defaults to now.
             *
             * @example "2023-11-07 05:31:56"
             */
            'timestamp' => ['nullable', new ValidTimestamp],
        ];
    }
}
