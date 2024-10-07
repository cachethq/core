<?php

declare(strict_types=1);

namespace Cachet\Data\Schedule;

use Cachet\Data\BaseData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Max;

final class UpdateScheduleData extends BaseData
{
    public function __construct(
        #[Max(255)]
        public readonly ?string $name = null,
        public readonly ?string $message = null,
        #[Date]
        public readonly ?string $scheduledAt = null,
        #[DataCollectionOf(ScheduleComponentData::class)]
        public readonly ?array $components = null,
    ) {}
}
