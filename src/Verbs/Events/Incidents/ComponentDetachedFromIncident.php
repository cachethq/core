<?php

namespace Cachet\Verbs\Events\Incidents;

use Cachet\Models\Incident;
use Cachet\Verbs\States\ComponentState;
use Cachet\Verbs\States\IncidentState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;

class ComponentDetachedFromIncident extends Event
{
    #[StateId(IncidentState::class)]
    public int $incident_id;

    #[StateId(ComponentState::class)]
    public int $component_id;

    public function __construct(
        int $incident_id,
        int $component_id,
    ) {
        $this->incident_id = $incident_id;
        $this->component_id = $component_id;
    }

    public function validate(IncidentState $incidentState, ComponentState $componentState): bool
    {
        return ! $incidentState->deleted
            && isset($incidentState->affected_components[$this->component_id]);
    }

    public function apply(IncidentState $incidentState, ComponentState $componentState): void
    {
        unset($incidentState->affected_components[$this->component_id]);

        $componentState->incident_ids = array_values(
            array_filter($componentState->incident_ids, fn ($id) => $id !== $this->incident_id)
        );
    }

    public function handle(): void
    {
        $incident = Incident::findOrFail($this->incident_id);
        $incident->components()->detach($this->component_id);
    }
}
