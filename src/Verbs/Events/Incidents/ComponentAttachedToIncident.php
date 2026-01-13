<?php

namespace Cachet\Verbs\Events\Incidents;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Incident;
use Cachet\Verbs\States\ComponentState;
use Cachet\Verbs\States\IncidentState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;

class ComponentAttachedToIncident extends Event
{
    #[StateId(IncidentState::class)]
    public int $incident_id;

    #[StateId(ComponentState::class)]
    public int $component_id;

    public function __construct(
        int $incident_id,
        int $component_id,
        public ComponentStatusEnum $component_status,
    ) {
        $this->incident_id = $incident_id;
        $this->component_id = $component_id;
    }

    public function validate(IncidentState $incidentState, ComponentState $componentState): bool
    {
        return ! $incidentState->deleted
            && ! $componentState->deleted
            && ! isset($incidentState->affected_components[$this->component_id]);
    }

    public function apply(IncidentState $incidentState, ComponentState $componentState): void
    {
        $incidentState->affected_components[$this->component_id] = $this->component_status;

        if (! in_array($this->incident_id, $componentState->incident_ids)) {
            $componentState->incident_ids[] = $this->incident_id;
        }
    }

    public function handle(): void
    {
        $incident = Incident::findOrFail($this->incident_id);
        $incident->components()->attach($this->component_id, [
            'component_status' => $this->component_status,
        ]);
    }
}
