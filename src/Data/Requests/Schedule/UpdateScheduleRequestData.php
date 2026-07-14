<?php

namespace Cachet\Data\Requests\Schedule;

use Cachet\Data\BaseData;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\ScheduleStatusEnum;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class UpdateScheduleRequestData extends BaseData
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $message = null,
        public readonly ?ScheduleStatusEnum $status = null,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i:s')]
        public readonly ?Carbon $scheduledAt = null,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i:s')]
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
            'scheduled_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
            'components' => ['array'],
            'components.*.id' => ['required_with:components', 'int', 'exists:components,id'],
            'components.*.status' => ['required_with:components', Rule::enum(ComponentStatusEnum::class)],
            'meta' => ['nullable', 'array'],
        ];
    }
}
