<?php

namespace Cachet\Data\Requests\Incident;

use Cachet\Data\BaseData;
use Cachet\Enums\IncidentStatusEnum;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class UpdateIncidentRequestData extends BaseData
{
    public function __construct(
        public readonly Optional|string $name,
        public readonly ?string $message = null,
        public readonly ?IncidentStatusEnum $status = null,
        public readonly ?bool $visible = null,
        public readonly ?bool $stickied = null,
        public readonly ?bool $notifications = null,
        public readonly ?string $occurredAt = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => ['string', 'max:255'],
            'message' => ['string'],
            'status' => [Rule::enum(IncidentStatusEnum::class)],
            'visible' => ['boolean'],
            'stickied' => ['boolean'],
            'notifications' => ['boolean'],
            'occurred_at' => ['nullable', 'string'],
        ];
    }

    public function toArray(): array
    {
        return parent::toArray();
    }
}
