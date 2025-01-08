<?php

namespace Cachet\Data\Requests\IncidentUpdate;

use Cachet\Data\BaseData;
use Cachet\Enums\IncidentStatusEnum;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class CreateIncidentUpdateRequestData extends BaseData
{
    public function __construct(
        public readonly IncidentStatusEnum $status,
        public readonly string $message,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'status' => ['required', Rule::enum(IncidentStatusEnum::class)],
            'message' => ['required', 'string'],
        ];
    }
}
