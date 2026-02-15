<?php

namespace Cachet\Verbs\Events\Incidents;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Events\Incidents\IncidentCreated as WebhookIncidentCreated;
use Cachet\Models\Incident;
use Cachet\Verbs\States\ComponentState;
use Cachet\Verbs\States\IncidentState;
use Illuminate\Support\Str;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;
use Thunk\Verbs\Facades\Verbs;

class IncidentCreated extends Event
{
    #[StateId(IncidentState::class)]
    public ?int $incident_id = null;

    /**
     * @param  array<int, array{id: int, status: ComponentStatusEnum}>  $components
     */
    public function __construct(
        public string $name,
        public IncidentStatusEnum $status,
        public string $message,
        public ResourceVisibilityEnum $visible = ResourceVisibilityEnum::guest,
        public bool $stickied = false,
        public bool $notifications = false,
        public ?string $occurred_at = null,
        public ?int $user_id = null,
        public ?string $external_provider = null,
        public ?string $external_id = null,
        public array $components = [],
        public ?string $guid = null,
    ) {}

    public function apply(IncidentState $state): void
    {
        $state->id = $this->incident_id;
        $state->guid = $this->guid ?? (string) Str::uuid();
        $state->name = $this->name;
        $state->status = $this->status;
        $state->message = $this->message;
        $state->visible = $this->visible;
        $state->stickied = $this->stickied;
        $state->notifications = $this->notifications;
        $state->occurred_at = $this->occurred_at;
        $state->user_id = $this->user_id;
        $state->external_provider = $this->external_provider;
        $state->external_id = $this->external_id;
        $state->deleted = false;

        foreach ($this->components as $component) {
            $state->affected_components[$component['id']] = $component['status'];

            $componentState = ComponentState::load($component['id']);
            if (! in_array($this->incident_id, $componentState->incident_ids)) {
                $componentState->incident_ids[] = $this->incident_id;
            }
        }

        $state->status_history[] = [
            'from' => null,
            'to' => $this->status,
            'at' => now()->toIso8601String(),
        ];
    }

    public function handle(IncidentState $state): Incident
    {
        if (config('verbs.migration_mode', false)) {
            return Incident::find($this->incident_id);
        }

        $incident = Incident::create([
            'guid' => $state->guid,
            'name' => $this->name,
            'status' => $this->status,
            'message' => $this->message,
            'visible' => $this->visible,
            'stickied' => $this->stickied,
            'notifications' => $this->notifications,
            'occurred_at' => $this->occurred_at,
            'user_id' => $this->user_id,
            'external_provider' => $this->external_provider,
            'external_id' => $this->external_id,
        ]);

        $this->incident_id = $incident->id;

        if (! empty($this->components)) {
            $pivotData = collect($this->components)->mapWithKeys(fn ($c) => [
                $c['id'] => ['component_status' => $c['status']],
            ])->all();
            $incident->components()->sync($pivotData);
        }

        Verbs::unlessReplaying(fn () => event(new WebhookIncidentCreated($incident)));

        return $incident;
    }
}
