<?php

namespace Cachet\Data\Requests\Schedule;

use Cachet\Data\BaseData;
use Cachet\Data\Casts\FlexibleDateTimeCast;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\ScheduleStatusEnum;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class UpdateScheduleRequestData extends BaseData
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $message = null,
        public readonly ?ScheduleStatusEnum $status = null,
        #[WithCast(FlexibleDateTimeCast::class)]
        public readonly ?Carbon $scheduledAt = null,
        #[WithCast(FlexibleDateTimeCast::class)]
        public readonly ?Carbon $completedAt = null,
        #[DataCollectionOf(ScheduleComponentRequestData::class)]
        public readonly ?array $components = null,
        /** @var array<string, mixed>|null */
        public readonly ?array $meta = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => ['string', 'max:255'],
            'message' => ['string'],
            /**
             * The date/time the maintenance window starts, e.g. "2023-11-07 05:31:56" or ISO 8601.
             */
            'scheduled_at' => ['nullable', 'date'],
            /**
             * The date/time the maintenance window ends, e.g. "2023-11-07 05:31:56" or ISO 8601.
             */
            'completed_at' => ['nullable', 'date'],
            'components' => ['array'],
            'components.*.id' => ['required', 'int', 'exists:components,id'],
            'components.*.status' => ['required', Rule::enum(ComponentStatusEnum::class)],
            /**
             * Key/value metadata to store against the resource.
             *
             * @var array<string, mixed>|null
             *
             * @example {"cluster": "eu-west"}
             */
            'meta' => ['nullable', 'array'],
        ];
    }
}
