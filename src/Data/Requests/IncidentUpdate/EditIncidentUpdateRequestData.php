<?php

namespace Cachet\Data\Requests\IncidentUpdate;

use Cachet\Data\BaseData;
use Cachet\Enums\IncidentStatusEnum;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class EditIncidentUpdateRequestData extends BaseData
{
    public function __construct(
        public readonly ?IncidentStatusEnum $status = null,
        public readonly ?string $message = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'status' => [Rule::enum(IncidentStatusEnum::class)],
            'message' => ['string'],
        ];
    }
}
