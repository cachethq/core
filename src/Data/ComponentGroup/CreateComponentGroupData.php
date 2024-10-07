<?php

namespace Cachet\Data\ComponentGroup;

use Cachet\Data\BaseData;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\ListType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Sometimes;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class CreateComponentGroupData extends BaseData
{
    public function __construct(
        #[Max(255), Required]
        public readonly string $name,
        #[IntegerType, Min(0)]
        public readonly ?int $order = null,
        public readonly ?bool $visible = null,
        #[ListType]
        public readonly ?array $components = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'components.*' => ['int', 'min:0', Rule::exists('components', 'id')],
        ];
    }
}
