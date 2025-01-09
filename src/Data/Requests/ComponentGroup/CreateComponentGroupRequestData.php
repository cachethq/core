<?php

namespace Cachet\Data\Requests\ComponentGroup;

use Cachet\Data\BaseData;
use Cachet\Enums\ResourceVisibilityEnum;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class CreateComponentGroupRequestData extends BaseData
{
    public function __construct(
        public readonly string $name,
        public readonly ?int $order = null,
        public readonly ?ResourceVisibilityEnum $visible = null,
        public readonly ?array $components = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'order' => ['int', 'min:0'],
            'visible' => ['bool'],
            'components' => ['array'],
            'components.*' => ['int', 'min:0', Rule::exists('components', 'id')],
        ];
    }
}
