<?php

declare(strict_types=1);

namespace Cachet\Data\ScheduleUpdate;

use Cachet\Data\BaseData;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class CreateScheduleUpdateRequestData extends BaseData
{
    public function __construct(
        public readonly string $message,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return ['message' => ['required', 'string']];
    }
}
