<?php

namespace Cachet\Verbs\Events\Incidents;

use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Incident;
use Cachet\Models\Update;
use Cachet\Verbs\States\IncidentState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;

class IncidentUpdateRecorded extends Event
{
    #[StateId(IncidentState::class)]
    public int $incident_id;

    public function __construct(
        int $incident_id,
        public IncidentStatusEnum $status,
        public string $message,
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
        $state->updates[] = [
            'status' => $this->status,
            'message' => $this->message,
            'user_id' => $this->user_id,
            'at' => now()->toIso8601String(),
        ];

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

    public function handle(IncidentState $state): Update
    {
        $incident = Incident::findOrFail($this->incident_id);

        $update = new Update([
            'status' => $this->status,
            'message' => $this->message,
            'user_id' => $this->user_id,
        ]);

        $incident->updates()->save($update);

        return $update;
    }
}
