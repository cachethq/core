<?php

namespace Cachet\Data\Component;

use Cachet\Data\BaseData;
use Cachet\Enums\ComponentStatusEnum;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;

final class CreateComponentData extends BaseData
{
    public function __construct(
        #[Max(255), Required]
        public readonly string $name,
        public readonly ?string $description = null,
        #[Enum(ComponentStatusEnum::class)]
        public readonly ?ComponentStatusEnum $status = null,
        public readonly ?string $link = null,
        #[Min(0)]
        public readonly ?int $order = null,
        public readonly bool $enabled = true,
        #[Min(0), Exists(table: 'component_groups', column: 'id')]
        public readonly ?int $componentGroupId = null,
    ) {}

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
