<?php

namespace Cachet\Data\Schedule;

use Cachet\Data\BaseData;
use Cachet\Enums\ScheduleStatusEnum;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;

final class CreateScheduleData extends BaseData
{
    public function __construct(
        #[Required, Max(255)]
        public readonly string $name,
        #[Required]
        public readonly string $message,
        #[Required, Date]
        public readonly string $scheduledAt,
        #[Enum(ScheduleStatusEnum::class)]
        public readonly ?ScheduleStatusEnum $status = null,
        #[DataCollectionOf(ScheduleComponentData::class)]
        public readonly ?array $components = null,
    ) {}
}
