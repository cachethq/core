<?php

declare(strict_types=1);

namespace Cachet\Data\Component;

use Cachet\Enums\ComponentStatusEnum;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class ComponentData extends Data
{
    public function __construct(
        #[Max(255), Required]
        public readonly string $name,
        public readonly ?string $description = null,
        public readonly ?ComponentStatusEnum $status = null,
        public readonly ?string $link = null,
        #[Min(0)]
        public readonly ?int $order = null,
        public readonly bool $enabled = true,
        #[Min(0), Exists(table: 'component_groups', column: 'id')]
        public readonly ?int $componentGroupId = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(parent::toArray());
    }
}
