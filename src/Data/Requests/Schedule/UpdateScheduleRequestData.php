<?php

namespace Cachet\Data\Requests\Schedule;

use Cachet\Data\BaseData;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\ScheduleStatusEnum;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class UpdateScheduleRequestData extends BaseData
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $message = null,
        public readonly ?ScheduleStatusEnum $status = null,
        public readonly ?string $scheduledAt = null,
        #[DataCollectionOf(ScheduleComponentRequestData::class)]
        public readonly ?array $components = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => ['string', 'max:255'],
            'message' => ['string'],
            'scheduled_at' => ['nullable', 'date'],
            'components' => ['array'],
            'components.*.id' => ['required_with:components', 'int', 'exists:components,id'],
            'components.*.status' => ['required_with:components', Rule::enum(ComponentStatusEnum::class)],
        ];
    }
}
