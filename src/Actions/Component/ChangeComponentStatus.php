<?php

namespace Cachet\Actions\Component;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\ComponentStatusSourceEnum;
use Cachet\Events\Components\ComponentStatusWasChanged;
use Cachet\Models\Component;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ChangeComponentStatus
{
    /**
     * Change a component's baseline status, recording what caused it.
     *
     * Every writer of the status column goes through here — the dashboard, the
     * API, MCP, the monitor and imports — so the status-changed event fires for
     * all of them rather than only the ones that happen to call an action, and
     * so there is always a record of who or what asserted the change.
     *
     * The status and its record are written together, and the event is only
     * announced once both have committed.
     *
     * Anything else the caller was going to save on the component is passed in
     * as `$attributes` and written in the same statement, so a single edit is
     * one write and one `ComponentUpdated` event rather than two.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function handle(
        Component $component,
        ComponentStatusEnum $status,
        ComponentStatusSourceEnum $source = ComponentStatusSourceEnum::Manual,
        Authenticatable|Model|null $causer = null,
        ?string $reason = null,
        array $attributes = [],
    ): Component {
        $oldStatus = $component->getAttribute('status');
        $changed = $oldStatus !== $status;

        DB::transaction(function () use ($component, $status, $source, $causer, $reason, $oldStatus, $changed, $attributes): void {
            $component->update([...$attributes, 'status' => $status]);

            if (! $changed) {
                return;
            }

            $component->statusChanges()->create([
                'old_status' => $oldStatus,
                'new_status' => $status,
                'source' => $source,
                'reason' => $reason,
                'causer_type' => $causer instanceof Model ? $causer->getMorphClass() : null,
                'causer_id' => $causer instanceof Model ? $causer->getKey() : null,
            ]);
        });

        if ($changed) {
            ComponentStatusWasChanged::dispatch($component, $oldStatus, $status, $source);
        }

        return $component;
    }
}
