<?php

namespace Cachet\Data\Requests\ScheduleUpdate;

use Cachet\Data\BaseData;
use Cachet\Data\Casts\FlexibleDateTimeCast;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class CreateScheduleUpdateRequestData extends BaseData
{
    public function __construct(
        public readonly string $message,
        #[WithCast(FlexibleDateTimeCast::class)]
        public readonly ?Carbon $completedAt = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'message' => ['required', 'string'],
            /**
             * The date/time the maintenance window ended, e.g. "2023-11-07 05:31:56" or ISO 8601.
             */
            'completed_at' => ['nullable', 'date'],
        ];
    }
}
