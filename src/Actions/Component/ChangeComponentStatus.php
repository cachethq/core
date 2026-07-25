<?php

namespace Cachet\Actions\Component;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\ComponentStatusSourceEnum;
use Cachet\Events\Components\ComponentStatusWasChanged;
use Cachet\Models\Component;
use Illuminate\Database\Eloquent\Model;

class ChangeComponentStatus
{
    /**
     * Change a component's baseline status, recording what caused it.
     *
     * Every writer of the status column goes through here — the dashboard, the
     * API, MCP, the monitor and imports — so the status-changed event fires for
     * all of them rather than only the ones that happen to call an action, and
     * so there is always a record of who or what asserted the change.
     */
    public function handle(
        Component $component,
        ComponentStatusEnum $status,
        ComponentStatusSourceEnum $source = ComponentStatusSourceEnum::Manual,
        ?Model $causer = null,
        ?string $reason = null,
    ): Component {
        $oldStatus = $component->getAttribute('status');

        if ($oldStatus === $status) {
            return $component;
        }

        $component->update(['status' => $status]);

        $component->statusChanges()->create([
            'old_status' => $oldStatus,
            'new_status' => $status,
            'source' => $source,
            'reason' => $reason,
            'causer_type' => $causer?->getMorphClass(),
            'causer_id' => $causer?->getKey(),
        ]);

        ComponentStatusWasChanged::dispatch($component, $oldStatus, $status, $source);

        return $component;
    }
}
