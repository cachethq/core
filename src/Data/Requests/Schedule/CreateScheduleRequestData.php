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

final class CreateScheduleRequestData extends BaseData
{
    public function __construct(
        public readonly string $name,
        public readonly string $message,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i:s')]
        public readonly Carbon $scheduledAt,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i:s')]
        public readonly ?Carbon $completedAt = null,
        public readonly ?ScheduleStatusEnum $status = null,
        #[DataCollectionOf(ScheduleComponentRequestData::class)]
        public readonly ?array $components = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'scheduled_at' => ['required', 'date'],
            'completed_at' => ['nullable', 'date'],
            'components' => ['array'],
            'components.*.id' => ['required_with:components', 'int', 'exists:components,id'],
            'components.*.status' => ['required_with:components', 'int', Rule::enum(ComponentStatusEnum::class)],
        ];
    }
}
