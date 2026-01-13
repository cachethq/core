<?php

namespace Cachet\Verbs\Events\Incidents;

use Cachet\Events\Incidents\IncidentDeleted as WebhookIncidentDeleted;
use Cachet\Models\Incident;
use Cachet\Verbs\States\ComponentState;
use Cachet\Verbs\States\IncidentState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;
use Thunk\Verbs\Facades\Verbs;

class IncidentDeleted extends Event
{
    #[StateId(IncidentState::class)]
    public int $incident_id;

    public function __construct(int $incident_id)
    {
        $this->incident_id = $incident_id;
    }

    public function validate(IncidentState $state): bool
    {
        return ! (isset($state->deleted) && $state->deleted);
    }

    public function apply(IncidentState $state): void
    {
        $state->deleted = true;

        // Remove incident from component states
        $affectedComponents = isset($state->affected_components) ? $state->affected_components : [];
        foreach (array_keys($affectedComponents) as $componentId) {
            $componentState = ComponentState::load($componentId);
            $componentState->incident_ids = array_values(
                array_filter($componentState->incident_ids ?? [], fn ($id) => $id !== $this->incident_id)
            );
        }
    }

    public function handle(IncidentState $state): void
    {
        $incident = Incident::findOrFail($this->incident_id);

        Verbs::unlessReplaying(fn () => event(new WebhookIncidentDeleted($incident)));

        // Delete related updates first
        $incident->updates()->delete();

        $incident->delete();
    }
}
