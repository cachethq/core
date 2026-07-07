<?php

namespace Cachet\Data\Requests\ScheduleUpdate;

use Cachet\Data\BaseData;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class CreateScheduleUpdateRequestData extends BaseData
{
    public function __construct(
        public readonly string $message,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i:s')]
        public readonly ?Carbon $completedAt = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'message' => ['required', 'string'],
            'completed_at' => ['nullable', 'date'],
        ];
    }
}
