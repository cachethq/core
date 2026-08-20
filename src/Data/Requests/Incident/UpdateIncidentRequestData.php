<?php

namespace Cachet\Data\Requests\Incident;

use Cachet\Data\BaseData;
use Cachet\Data\Casts\FlexibleDateTimeCast;
use Cachet\Enums\IncidentStatusEnum;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\WithCast;
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
        #[WithCast(FlexibleDateTimeCast::class)]
        public readonly ?Carbon $occurredAt = null,
        #[WithCast(FlexibleDateTimeCast::class)]
        public readonly ?Carbon $publishedAt = null,
        /** @var array<string, mixed>|null */
        public readonly ?array $meta = null,
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
            /**
             * The date/time the incident occurred, e.g. "2023-11-07 05:31:56" or ISO 8601.
             */
            'occurred_at' => ['nullable', 'date'],
            /**
             * The date/time to publish the incident, e.g. "2023-11-07 05:31:56" or ISO 8601. While set in the future the incident is hidden from the status page and public API.
             */
            'published_at' => ['nullable', 'date'],
            /**
             * Key/value metadata to store against the resource.
             *
             * @var array<string, mixed>|null
             *
             * @example {"cluster": "eu-west"}
             */
            'meta' => ['nullable', 'array'],
        ];
    }

    public function toArray(): array
    {
        return parent::toArray();
    }
}
