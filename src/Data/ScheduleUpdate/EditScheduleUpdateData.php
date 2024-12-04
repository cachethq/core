<?php

declare(strict_types=1);

namespace Cachet\Data\ScheduleUpdate;

use Cachet\Data\BaseData;

class EditScheduleUpdateData extends BaseData
{
    public function __construct(
        public readonly ?string $message = null,
    ) {}
}
