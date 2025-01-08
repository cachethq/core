<?php

namespace Cachet\Data\Requests\Metric;

use Cachet\Data\BaseData;
use Cachet\Enums\MetricTypeEnum;
use Cachet\Rules\FactorOfSixty;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class CreateMetricRequestData extends BaseData
{
    public function __construct(
        public readonly string $name,
        public readonly string $suffix,
        public readonly ?MetricTypeEnum $calcType = null,
        public readonly ?string $description = null,
        public readonly ?float $defaultValue = null,
        public readonly ?bool $displayChart = null,
        public readonly ?int $threshold = null,
        public readonly ?int $places = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'suffix' => ['required', 'string', 'max:255'],
            'description' => ['string'],
            'default_value' => ['decimal:1,2'],
            'threshold' => ['int', 'min:0', 'max:60', new FactorOfSixty],
        ];
    }
}
