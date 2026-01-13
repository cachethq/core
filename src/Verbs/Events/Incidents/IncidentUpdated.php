<?php

namespace Cachet\Verbs\Events\Incidents;

use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Events\Incidents\IncidentUpdated as WebhookIncidentUpdated;
use Cachet\Models\Incident;
use Cachet\Verbs\States\IncidentState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;
use Thunk\Verbs\Facades\Verbs;

class IncidentUpdated extends Event
{
    #[StateId(IncidentState::class)]
    public int $incident_id;

    public function __construct(
        int $incident_id,
        public ?string $name = null,
        public ?IncidentStatusEnum $status = null,
        public ?string $message = null,
        public ?ResourceVisibilityEnum $visible = null,
        public ?bool $stickied = null,
        public ?bool $notifications = null,
        public ?string $occurred_at = null,
        public ?int $user_id = null,
    ) {
        $this->incident_id = $incident_id;
    }

    public function validate(IncidentState $state): bool
    {
        return ! (isset($state->deleted) && $state->deleted);
    }

    public function apply(IncidentState $state): void
    {
        if ($this->name !== null) {
            $state->name = $this->name;
        }
        if ($this->message !== null) {
            $state->message = $this->message;
        }
        if ($this->visible !== null) {
            $state->visible = $this->visible;
        }
        if ($this->stickied !== null) {
            $state->stickied = $this->stickied;
        }
        if ($this->notifications !== null) {
            $state->notifications = $this->notifications;
        }
        if ($this->occurred_at !== null) {
            $state->occurred_at = $this->occurred_at;
        }
        if ($this->user_id !== null) {
            $state->user_id = $this->user_id;
        }
        if ($this->status !== null) {
            $previousStatus = isset($state->status) ? $state->status : null;
            if ($previousStatus !== $this->status) {
                $state->status_history[] = [
                    'from' => $previousStatus,
                    'to' => $this->status,
                    'at' => now()->toIso8601String(),
                ];
                $state->status = $this->status;
            }
        }
    }

    public function handle(IncidentState $state): Incident
    {
        $incident = Incident::findOrFail($this->incident_id);

        $updates = array_filter([
            'name' => $this->name,
            'status' => $this->status,
            'message' => $this->message,
            'visible' => $this->visible,
            'stickied' => $this->stickied,
            'notifications' => $this->notifications,
            'occurred_at' => $this->occurred_at,
            'user_id' => $this->user_id,
        ], fn ($value) => $value !== null);

        if (! empty($updates)) {
            $incident->update($updates);
        }

        Verbs::unlessReplaying(fn () => event(new WebhookIncidentUpdated($incident)));

        return $incident->fresh();
    }
}
