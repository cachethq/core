<?php

declare(strict_types=1);

namespace Cachet\Data\IncidentUpdate;

use Cachet\Data\BaseData;
use Cachet\Enums\IncidentStatusEnum;
use Spatie\LaravelData\Attributes\Validation\Required;

final class CreateIncidentUpdateData extends BaseData
{
    public function __construct(
        #[Required]
        public readonly IncidentStatusEnum $status,
        #[Required]
        public readonly string $message,
    ) {}
}
