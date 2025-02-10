<?php

namespace Cachet\Data\Requests\Schedule;

use Cachet\Data\BaseData;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use Spatie\LaravelData\Attributes\Validation\Exists;

final class ScheduleComponentRequestData extends BaseData
{
    public function __construct(
        #[Exists(Component::class, 'id')]
        public int $id,
        public ComponentStatusEnum $status,
    ) {}
}
