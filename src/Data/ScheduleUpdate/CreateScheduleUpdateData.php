<?php

declare(strict_types=1);

namespace Cachet\Data\ScheduleUpdate;

use Cachet\Data\BaseData;
use Spatie\LaravelData\Attributes\Validation\Required;

class CreateScheduleUpdateData extends BaseData
{
    public function __construct(
        #[Required]
        public readonly string $message,
    ) {}
}
