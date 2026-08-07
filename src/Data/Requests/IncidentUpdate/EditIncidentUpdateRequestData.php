<?php

namespace Cachet\Data\Requests\IncidentUpdate;

use Cachet\Data\BaseData;
use Cachet\Enums\IncidentStatusEnum;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class EditIncidentUpdateRequestData extends BaseData
{
    public function __construct(
        public readonly Optional|IncidentStatusEnum|null $status = new Optional,
        public readonly Optional|string $message = new Optional,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'status' => ['nullable', Rule::enum(IncidentStatusEnum::class)],
            'message' => ['string'],
        ];
    }

    public function toArray(): array
    {
        $attributes = [];

        if (! $this->status instanceof Optional) {
            $attributes['status'] = $this->status;
        }

        if (! $this->message instanceof Optional) {
            $attributes['message'] = $this->message;
        }

        return $attributes;
    }
}
