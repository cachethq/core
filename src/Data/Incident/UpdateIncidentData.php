<?php

namespace Cachet\Data\Incident;

use Cachet\Data\BaseData;
use Cachet\Enums\IncidentStatusEnum;
use Spatie\LaravelData\Attributes\Validation\Max;

final class UpdateIncidentData extends BaseData
{
    public function __construct(
        #[Max(255)]
        public readonly string $name,
        public readonly ?string $message = null,
        public readonly ?IncidentStatusEnum $status = null,
        public readonly ?bool $visible = null,
        public readonly ?bool $stickied = null,
        public readonly ?bool $notifications = null,
        public readonly ?string $occurredAt = null,
    ) {}
}
