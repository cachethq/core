<?php

namespace Cachet\Data\Incident;

use Cachet\Data\BaseData;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Component;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\RequiredWithout;

final class CreateIncidentData extends BaseData
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
        #[Exists(Component::class, 'id')]
        public readonly ?int $componentId = null,
        #[Enum(ComponentStatusEnum::class)]
        public readonly ?ComponentStatusEnum $componentStatus = null,
    ) {}

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
            componentId: $this->componentId,
            componentStatus: $this->componentStatus,
        );
    }
}
