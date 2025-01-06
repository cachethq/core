<?php

namespace Cachet\Data\Schedule;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class ScheduleComponentRequestData extends Data
{
    public function __construct(
        #[Exists(Component::class, 'id')]
        public int $id,
        public ComponentStatusEnum $status,
    ) {}
}
