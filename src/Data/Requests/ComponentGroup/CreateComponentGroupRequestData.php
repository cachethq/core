<?php

namespace Cachet\Data\Requests\ComponentGroup;

use Cachet\Data\BaseData;
use Cachet\Enums\ComponentGroupVisibilityEnum;
use Cachet\Enums\ResourceOrderColumnEnum;
use Cachet\Enums\ResourceOrderDirectionEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class CreateComponentGroupRequestData extends BaseData
{
    public function __construct(
        public readonly string $name,
        public readonly ?int $order = null,
        public readonly ?ResourceVisibilityEnum $visible = null,
        public readonly ?ComponentGroupVisibilityEnum $collapsed = null,
        public readonly ?ResourceOrderColumnEnum $orderColumn = null,
        public readonly ?ResourceOrderDirectionEnum $orderDirection = null,
        public readonly ?array $components = null,
        /** @var array<string, mixed>|null */
        public readonly ?array $meta = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'order' => ['int', 'min:0'],
            'visible' => ['bool'],
            'collapsed' => [Rule::enum(ComponentGroupVisibilityEnum::class)],
            'order_column' => [Rule::enum(ResourceOrderColumnEnum::class)],
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
            'meta' => ['nullable', 'array'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'collapsed' => [
                'description' => 'The collapsed state of the component group on the status page.',
                'example' => '0',
                'required' => false,
                'schema' => [
                    'type' => 'integer',
                    'enum' => ComponentGroupVisibilityEnum::cases(),
                ],
            ],
        ];
    }
}
