<?php

namespace Cachet\Data\Requests\ScheduleUpdate;

use Cachet\Data\BaseData;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class CreateScheduleUpdateRequestData extends BaseData
{
    public function __construct(
        public readonly string $message,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return ['message' => ['required', 'string']];
    }
}
