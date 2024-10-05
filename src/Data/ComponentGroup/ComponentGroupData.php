<?php

declare(strict_types=1);

namespace Cachet\Data\ComponentGroup;

use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class ComponentGroupData extends Data
{
    public function __construct(
        #[Max(255)]
        public readonly string $name,
        #[IntegerType, Min(0)]
        public readonly ?int $order = null,
        public readonly ?bool $visible = null,
        public readonly ?array $components = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'components' => ['array'],
            'components.*' => ['int', 'min:0', Rule::exists('components', 'id')],
        ];
    }

    public function toArray(): array
    {
        return array_filter(parent::toArray());
    }
}
