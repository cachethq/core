<?php

namespace Cachet\Data\Requests\Incident;

use Cachet\Data\BaseData;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use Spatie\LaravelData\Attributes\Validation\Exists;

final class IncidentComponentRequestData extends BaseData
{
    public function __construct(
        #[Exists(Component::class, 'id')]
        public int $id,
        public ComponentStatusEnum $status,
    ) {}
}
