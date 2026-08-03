<?php

namespace Cachet\Data\Requests\Incident;

use Cachet\Data\BaseUpdateData;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class UpdateIncidentRequestData extends BaseUpdateData
{
    public function __construct(
        public readonly string|Optional $name = new Optional,
        public readonly string|Optional $message = new Optional,
        public readonly IncidentStatusEnum|Optional $status = new Optional,
        public readonly ResourceVisibilityEnum|Optional $visible = new Optional,
        public readonly bool|Optional $stickied = new Optional,
        public readonly bool|Optional $notifications = new Optional,
        public readonly string|Optional|null $occurredAt = new Optional,
        public readonly string|Optional|null $publishedAt = new Optional,
        /** @var array<string, mixed>|Optional|null */
        public readonly array|Optional|null $meta = new Optional,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => ['string', 'max:255'],
            'message' => ['string'],
            'status' => [Rule::enum(IncidentStatusEnum::class)],
            /**
             * Who the incident is visible to: 0 authenticated users only, 1 everyone, 2 hidden.
             */
            'visible' => [Rule::enum(ResourceVisibilityEnum::class)],
            'stickied' => ['boolean'],
            'notifications' => ['boolean'],
            /**
             * The date/time the incident occurred, e.g. "2023-11-07 05:31:56" or ISO 8601. Send null to clear it.
             */
            'occurred_at' => ['nullable', 'date'],
            /**
             * The date/time to publish the incident, e.g. "2023-11-07 05:31:56" or ISO 8601. While set in the future the incident is hidden from the status page and public API. Send null to publish immediately.
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
}
