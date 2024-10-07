<?php

declare(strict_types=1);

namespace Cachet\Data\Incident;

use Cachet\Enums\IncidentStatusEnum;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

final class UpdateIncidentData extends Data
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

    public function toArray(): array
    {
        return array_filter(parent::toArray());
    }
}
