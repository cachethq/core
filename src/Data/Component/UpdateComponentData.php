<?php

namespace Cachet\Data\Component;

use Cachet\Data\BaseData;
use Cachet\Enums\ComponentStatusEnum;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;

final class UpdateComponentData extends BaseData
{
    public function __construct(
        #[Max(255)]
        public readonly ?string $name = null,
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
}
