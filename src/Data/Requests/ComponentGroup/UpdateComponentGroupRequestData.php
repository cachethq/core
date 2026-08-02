<?php

namespace Cachet\Data\Requests\ComponentGroup;

use Cachet\Data\BaseUpdateData;
use Cachet\Enums\ComponentGroupVisibilityEnum;
use Cachet\Enums\ResourceOrderColumnEnum;
use Cachet\Enums\ResourceOrderDirectionEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class UpdateComponentGroupRequestData extends BaseUpdateData
{
    public function __construct(
        public readonly string|Optional $name = new Optional,
        public readonly int|Optional $order = new Optional,
        public readonly ResourceVisibilityEnum|Optional $visible = new Optional,
        public readonly ComponentGroupVisibilityEnum|Optional $collapsed = new Optional,
        public readonly ResourceOrderColumnEnum|Optional|null $orderColumn = new Optional,
        public readonly ResourceOrderDirectionEnum|Optional|null $orderDirection = new Optional,
        /** @var array<int, int>|Optional */
        public readonly array|Optional $components = new Optional,
        /** @var array<string, mixed>|Optional|null */
        public readonly array|Optional|null $meta = new Optional,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => ['string', 'max:255'],
            'order' => ['int', 'min:0'],
            /**
             * Who the group is visible to: 0 authenticated users only, 1 everyone, 2 hidden.
             */
            'visible' => [Rule::enum(ResourceVisibilityEnum::class)],
            'collapsed' => [Rule::enum(ComponentGroupVisibilityEnum::class)],
            'order_column' => ['nullable', Rule::enum(ResourceOrderColumnEnum::class)],
            'order_direction' => [
                'nullable',
                Rule::requiredIf(function () use ($context) {
                    $column = $context->payload['order_column'] ?? null;

                    return filled($column) && $column !== ResourceOrderColumnEnum::Manual->value;
                }),
                Rule::enum(ResourceOrderDirectionEnum::class),
            ],
            'components' => ['array'],
            'components.*' => ['int', 'min:0', Rule::exists('components', 'id')],
            /**
             * Key/value metadata to store against the resource.
             *
             * @var array<string, mixed>|null
             *
             * @example {"cluster": "eu-west"}
             */
            'meta' => ['nullable', 'array'],
        ];
    }
}
