<?php

namespace Cachet\Data\Requests\ScheduleUpdate;

use Cachet\Data\BaseData;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class EditScheduleUpdateRequestData extends BaseData
{
    public function __construct(
        public readonly ?string $message = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return ['message' => ['string']];
    }
}
