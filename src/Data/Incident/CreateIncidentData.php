<?php

declare(strict_types=1);

namespace Cachet\Data\Incident;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Component;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\RequiredWithout;
use Spatie\LaravelData\Data;

final class CreateIncidentData extends Data
{
    public function __construct(
        #[Max(255)]
        public readonly string $name,
        public readonly IncidentStatusEnum $status,
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
        public readonly ?ComponentStatusEnum $componentStatus = null,
    ) {}

    public function withMessage(string $message): self
    {
        return new self(
            name: $this->name,
            message: $message,
            template: $this->template,
            status: $this->status,
            visible: $this->visible,
            stickied: $this->stickied,
            notifications: $this->notifications,
            occurredAt: $this->occurredAt,
            templateVars: $this->templateVars,
            componentId: $this->componentId,
            componentStatus: $this->componentStatus,
        );
    }

    public function toArray(): array
    {
        return array_filter(parent::toArray());
    }
}
