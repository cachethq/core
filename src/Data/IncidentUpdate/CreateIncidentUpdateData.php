<?php

namespace Cachet\Data\IncidentUpdate;

use Cachet\Data\BaseData;
use Cachet\Enums\IncidentStatusEnum;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Required;

final class CreateIncidentUpdateData extends BaseData
{
    public function __construct(
        #[Required, Enum(IncidentStatusEnum::class)]
        public readonly IncidentStatusEnum $status,
        #[Required]
        public readonly string $message,
    ) {}
}
