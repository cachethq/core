<?php

namespace Cachet\Data\Component;

use Cachet\Data\BaseData;
use Cachet\Enums\ComponentStatusEnum;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class CreateComponentRequestData extends BaseData
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description = null,
        public readonly ?ComponentStatusEnum $status = null,
        public readonly ?string $link = null,
        public readonly ?int $order = null,
        public readonly bool $enabled = true,
        public readonly ?int $componentGroupId = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => ['string', 'required', 'max:255'],
            'description' => ['string'],
            'status' => [Rule::enum(ComponentStatusEnum::class)],
            'link' => ['string'],
            'order' => ['int', 'min:0'],
            'enabled' => ['boolean'],
            'component_group_id' => ['int', 'min:0', Rule::exists('component_groups', 'id')],
        ];
    }

    /**
     * Specify body parameter documentation for Scribe.
     */
    public function bodyParameters(): array
    {
        return [
            'status' => [
                'description' => 'The status of the component. See [Component Statuses](/v3.x/guide/components#component-statuses) for more information.',
                'example' => '1',
                'required' => false,
                'schema' => [
                    'type' => 'integer',
                    'enum' => ComponentStatusEnum::cases(),
                ],
            ],
        ];
    }
}