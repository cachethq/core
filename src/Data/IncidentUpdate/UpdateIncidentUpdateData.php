<?php

namespace Cachet\Data\IncidentUpdate;

use Cachet\Data\BaseData;
use Cachet\Enums\IncidentStatusEnum;

final class UpdateIncidentUpdateData extends BaseData
{
    public function __construct(
        public readonly ?IncidentStatusEnum $status = null,
        public readonly ?string $message = null,
    ) {}
}
