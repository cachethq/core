<?php

namespace Cachet\Data\Schedule;

use Cachet\Data\BaseData;
use Cachet\Enums\ScheduleStatusEnum;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Max;

final class UpdateScheduleData extends BaseData
{
    public function __construct(
        #[Max(255)]
        public readonly ?string $name = null,
        public readonly ?string $message = null,
        #[Enum(ScheduleStatusEnum::class)]
        public readonly ?ScheduleStatusEnum $status = null,
        #[Date]
        public readonly ?string $scheduledAt = null,
        #[DataCollectionOf(ScheduleComponentData::class)]
        public readonly ?array $components = null,
    ) {}
}
