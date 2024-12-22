<?php

namespace Cachet\Data\IncidentUpdate;

use Cachet\Data\BaseData;
use Cachet\Enums\IncidentStatusEnum;
use Spatie\LaravelData\Attributes\Validation\Enum;

final class EditIncidentUpdateData extends BaseData
{
    public function __construct(
        #[Enum(IncidentStatusEnum::class)]
        public readonly ?IncidentStatusEnum $status = null,
        public readonly ?string $message = null,
    ) {}
}
