<?php

declare(strict_types=1);

namespace Cachet\Data\IncidentUpdate;

use Cachet\Data\BaseData;
use Cachet\Enums\IncidentStatusEnum;
use Spatie\LaravelData\Attributes\Validation\Required;

final class UpdateIncidentUpdateData extends BaseData
{
    public function __construct(
        public readonly ?IncidentStatusEnum $status = null,
        public readonly ?string $message = null,
    ) {}
}
