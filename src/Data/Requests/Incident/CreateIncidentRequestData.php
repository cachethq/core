<?php

namespace Cachet\Data\Requests\Incident;

use Cachet\Data\BaseData;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Component;
use Illuminate\Validation\Rule;
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
        public readonly ?IncidentStatusEnum $status = null,
        #[RequiredWithout('template')]
        public readonly ?string $message = null,
        #[RequiredWithout('message')]
        public readonly ?string $template = null,
        public readonly bool $visible = false,
        public readonly bool $stickied = false,
        public readonly bool $notifications = false,
        public readonly ?string $occurredAt = null,
        public readonly array $templateVars = [],
        public readonly array $components = [],
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'message' => ['required_without:template', 'string'],
            'template' => ['required_without:message', 'string'],
            'status' => ['required', Rule::enum(IncidentStatusEnum::class)],
            'visible' => ['boolean'],
            'stickied' => ['boolean'],
            'notifications' => ['boolean'],
            'occurred_at' => ['nullable', 'string'],
            'template_vars' => ['array'],
            'components' => ['array'],
            'components.*.component_id' => [Rule::exists('components', 'id')],
            'components.*.component_status' => ['nullable', Rule::enum(ComponentStatusEnum::class), 'required_with:component_id'],
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
            templateVars: $this->templateVars,
            components: $this->components,
        );
    }
}
