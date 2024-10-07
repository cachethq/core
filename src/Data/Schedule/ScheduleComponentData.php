<?php

namespace Cachet\Data\Schedule;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;

final class ScheduleComponentData extends Data
{
    public function __construct(
        #[Exists(Component::class, 'id')]
        public int $id,
        public ComponentStatusEnum $status,
    ) {}
}
