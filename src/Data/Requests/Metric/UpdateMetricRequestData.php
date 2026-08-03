<?php

namespace Cachet\Data\Requests\Metric;

use Cachet\Data\BaseUpdateData;
use Cachet\Rules\FactorOfSixty;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class UpdateMetricRequestData extends BaseUpdateData
{
    public function __construct(
        public readonly string|Optional $name = new Optional,
        public readonly string|Optional $suffix = new Optional,
        public readonly string|Optional|null $description = new Optional,
        public readonly float|Optional|null $defaultValue = new Optional,
        public readonly int|Optional $threshold = new Optional,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => ['string', 'max:255'],
            'suffix' => ['string', 'max:255'],
            'description' => ['nullable', 'string'],
            'default_value' => ['nullable', 'decimal:1,2'],
            'threshold' => ['int', 'min:0', 'max:60', new FactorOfSixty],
        ];
    }
}
