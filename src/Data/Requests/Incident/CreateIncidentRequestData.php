<?php

namespace Cachet\Data\Requests\Incident;

use Cachet\Data\BaseData;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\Component;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\RequiredWithout;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class CreateIncidentRequestData extends BaseData
{
    public function __construct(
        #[Max(255)]
        public readonly string $name,
        #[Enum(IncidentStatusEnum::class)]
        public readonly IncidentStatusEnum $status,
        #[RequiredWithout('template')]
        public readonly ?string $message = null,
        #[RequiredWithout('message')]
        public readonly ?string $template = null,
        public readonly ResourceVisibilityEnum $visible = ResourceVisibilityEnum::authenticated,
        public readonly bool $stickied = false,
        public readonly bool $notifications = false,
        public readonly ?string $occurredAt = null,
        public readonly ?string $publishedAt = null,
        public readonly array $templateVars = [],
        #[Exists(Component::class, 'id')]
        public readonly ?int $componentId = null,
        #[Enum(ComponentStatusEnum::class)]
        public readonly ?ComponentStatusEnum $componentStatus = null,
        #[DataCollectionOf(IncidentComponentRequestData::class)]
        public readonly ?array $components = null,
        /** @var array<string, mixed>|null */
        public readonly ?array $meta = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'message' => ['required_without:template', 'string'],
            'template' => ['required_without:message', 'string', Rule::exists('incident_templates', 'slug')],
            'status' => ['required', Rule::enum(IncidentStatusEnum::class)],
            /**
             * Who the incident is visible to: 0 authenticated users only, 1 everyone, 2 hidden. Defaults to authenticated users only.
             */
            'visible' => [Rule::enum(ResourceVisibilityEnum::class)],
            'stickied' => ['boolean'],
            'notifications' => ['boolean'],
            /**
             * The date/time the incident occurred, e.g. "2023-11-07 05:31:56" or ISO 8601. Defaults to now.
             */
            'occurred_at' => ['nullable', 'date'],
            /**
             * The date/time to publish the incident, e.g. "2023-11-07 05:31:56" or ISO 8601. While set in the future the incident is hidden from the status page and public API. Defaults to published immediately.
             */
            'published_at' => ['nullable', 'date'],
            /**
             * Key/value variables passed to the incident template when rendering the message.
             *
             * @var array<string, mixed>
             *
             * @example {"reason": "scheduled maintenance"}
             */
            'template_vars' => ['array'],
            'component_id' => [Rule::exists('components', 'id')],
            'component_status' => ['nullable', Rule::enum(ComponentStatusEnum::class), 'required_with:component_id'],
            'components' => ['array'],
            'components.*.id' => ['required', 'int', 'distinct', 'exists:components,id'],
            'components.*.status' => ['required', 'int', Rule::enum(ComponentStatusEnum::class)],
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

    public function withMessage(string $message): self
    {
        return new self(
            name: $this->name,
            status: $this->status,
            message: $message,
            template: $this->template,
            visible: $this->visible,
            stickied: $this->stickied,
            notifications: $this->notifications,
            occurredAt: $this->occurredAt,
            publishedAt: $this->publishedAt,
            templateVars: $this->templateVars,
            componentId: $this->componentId,
            componentStatus: $this->componentStatus,
            components: $this->components,
            meta: $this->meta,
        );
    }
}
