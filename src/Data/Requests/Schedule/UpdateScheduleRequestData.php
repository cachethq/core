<?php

namespace Cachet\Data\Requests\Schedule;

use Cachet\Data\BaseUpdateData;
use Cachet\Data\Casts\FlexibleDateTimeCast;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\ScheduleStatusEnum;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class UpdateScheduleRequestData extends BaseUpdateData
{
    public function __construct(
        public readonly string|Optional $name = new Optional,
        public readonly string|Optional|null $message = new Optional,
        public readonly ScheduleStatusEnum|Optional $status = new Optional,
        #[WithCast(FlexibleDateTimeCast::class)]
        public readonly Carbon|Optional $scheduledAt = new Optional,
        #[WithCast(FlexibleDateTimeCast::class)]
        public readonly Carbon|Optional|null $completedAt = new Optional,
        #[WithCast(FlexibleDateTimeCast::class)]
        public readonly Carbon|Optional|null $publishedAt = new Optional,
        /** @var array<int, ScheduleComponentRequestData>|Optional */
        #[DataCollectionOf(ScheduleComponentRequestData::class)]
        public readonly array|Optional $components = new Optional,
        /** @var array<string, mixed>|Optional|null */
        public readonly array|Optional|null $meta = new Optional,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => ['string', 'max:255'],
            'message' => ['nullable', 'string'],
            /**
             * The date/time the maintenance window starts, e.g. "2023-11-07 05:31:56" or ISO 8601.
             */
            'scheduled_at' => ['date'],
            /**
             * The date/time the maintenance window ends, e.g. "2023-11-07 05:31:56" or ISO 8601. Send null to reopen the window.
             */
            'completed_at' => ['nullable', 'date'],
            /**
             * The date/time to publish the maintenance, e.g. "2023-11-07 05:31:56" or ISO 8601. While set in the future the maintenance is hidden from the status page and public API. Send null to publish immediately.
             */
            'published_at' => ['nullable', 'date'],
            'components' => ['array'],
            'components.*.id' => ['required', 'int', 'distinct', 'exists:components,id'],
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
