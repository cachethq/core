<?php

namespace Cachet\Data;

use Cachet\Enums\ComponentStatusEnum;

final class ComponentData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description = null,
        public readonly ?ComponentStatusEnum $status = null,
        public readonly ?string $link = null,
        public readonly ?int $order = null,
        public readonly ?int $componentGroupId = null,
        public readonly bool $enabled = true,
    ) {
    }
}
