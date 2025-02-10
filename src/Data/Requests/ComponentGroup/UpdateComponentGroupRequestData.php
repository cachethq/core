<?php

namespace Cachet\Data\Requests\ComponentGroup;

use Cachet\Data\BaseData;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class UpdateComponentGroupRequestData extends BaseData
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?int $order = null,
        public readonly ?bool $visible = null,
        public readonly ?array $components = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => ['string', 'max:255'],
            'order' => ['int', 'min:0'],
            'visible' => ['bool'],
            'components' => ['array'],
            'components.*' => ['int', 'min:0', Rule::exists('components', 'id')],
        ];
    }
}
