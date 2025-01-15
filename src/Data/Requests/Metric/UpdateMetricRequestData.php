<?php

namespace Cachet\Data\Requests\Metric;

use Cachet\Data\BaseData;
use Cachet\Rules\FactorOfSixty;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class UpdateMetricRequestData extends BaseData
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $suffix = null,
        public readonly ?string $description = null,
        public readonly ?float $defaultValue = null,
        public readonly ?int $threshold = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => ['string', 'max:255'],
            'suffix' => ['string', 'max:255'],
            'description' => ['string'],
            'default_value' => ['decimal:1,2'],
            'threshold' => ['int', 'min:0', 'max:60', new FactorOfSixty],
        ];
    }
}
