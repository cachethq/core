<?php

use Cachet\Enums\ComponentGroupVisibilityEnum;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Cachet\Models\Incident;
use Cachet\Models\Schedule;
use Cachet\Verbs\Events\ComponentGroups\ComponentGroupCreated;
use Cachet\Verbs\Events\ComponentGroups\ComponentGroupDeleted;
use Cachet\Verbs\Events\Components\ComponentCreated;
use Cachet\Verbs\Events\Components\ComponentDeleted;
use Cachet\Verbs\Events\Incidents\IncidentCreated;
use Cachet\Verbs\Events\Incidents\IncidentDeleted;
use Cachet\Verbs\Events\Incidents\IncidentUpdateRecorded;
use Cachet\Verbs\Events\Schedules\ScheduleCreated;
use Cachet\Verbs\Events\Schedules\ScheduleDeleted;
use Cachet\Verbs\Events\Schedules\ScheduleUpdateRecorded;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Thunk\Verbs\Facades\Verbs;

return new class extends Migration
{
    public function up(): void
    {
        // Skip if events already exist
        if (DB::table('verb_events')->exists()) {
            return;
        }

        $this->migrateComponentGroups();
        $this->migrateComponents();
        $this->migrateIncidents();
        $this->migrateSchedules();

        Verbs::commit();
    }

    public function down(): void
    {
        // Truncate Verbs tables to remove migrated events
        DB::table('verb_state_events')->truncate();
        DB::table('verb_snapshots')->truncate();
        DB::table('verb_events')->truncate();
    }

    private function migrateComponentGroups(): void
    {
        ComponentGroup::each(function (ComponentGroup $group) {
            ComponentGroupCreated::fire(
                component_group_id: $group->id,
                name: $group->name,
                order: $group->order ?? 0,
                collapsed: $group->collapsed ?? ComponentGroupVisibilityEnum::expanded,
                visible: $group->visible ?? ResourceVisibilityEnum::guest,
            );
        });
    }

    private function migrateComponents(): void
    {
        Component::withTrashed()->each(function (Component $component) {
            ComponentCreated::fire(
                component_id: $component->id,
                name: $component->name,
                status: $component->status ?? ComponentStatusEnum::operational,
                description: $component->description,
                link: $component->link,
                order: $component->order ?? 0,
                component_group_id: $component->component_group_id,
                enabled: $component->enabled ?? true,
                meta: $component->meta ?? [],
            );

            if ($component->deleted_at) {
                ComponentDeleted::fire(component_id: $component->id);
            }
        });
    }

    private function migrateIncidents(): void
    {
        Incident::withTrashed()->with(['components', 'updates'])->each(function (Incident $incident) {
            $components = $incident->components->map(fn ($c) => [
                'id' => $c->id,
                'status' => $c->pivot->component_status ?? ComponentStatusEnum::operational,
            ])->all();

            IncidentCreated::fire(
                incident_id: $incident->id,
                name: $incident->name,
                status: $incident->status ?? IncidentStatusEnum::investigating,
                message: $incident->message ?? '',
                visible: $incident->visible ?? ResourceVisibilityEnum::guest,
                stickied: $incident->stickied ?? false,
                notifications: boolval($incident->notifications) ?? false,
                occurred_at: $incident->occurred_at?->toIso8601String(),
                user_id: $incident->user_id,
                external_provider: $incident->external_provider,
                external_id: $incident->external_id,
                components: $components,
                guid: $incident->guid,
            );

            foreach ($incident->updates as $update) {
                IncidentUpdateRecorded::fire(
                    incident_id: $incident->id,
                    status: $update->status ?? IncidentStatusEnum::investigating,
                    message: $update->message ?? '',
                    user_id: $update->user_id,
                );
            }

            if ($incident->deleted_at) {
                IncidentDeleted::fire(incident_id: $incident->id);
            }
        });
    }

    private function migrateSchedules(): void
    {
        Schedule::withTrashed()->with(['components', 'updates'])->each(function (Schedule $schedule) {
            $components = $schedule->components->map(fn ($c) => [
                'id' => $c->id,
                'status' => $c->pivot->component_status ?? ComponentStatusEnum::operational,
            ])->all();

            ScheduleCreated::fire(
                schedule_id: $schedule->id,
                name: $schedule->name,
                message: $schedule->message,
                scheduled_at: $schedule->scheduled_at?->toIso8601String(),
                completed_at: $schedule->completed_at?->toIso8601String(),
                components: $components,
            );

            foreach ($schedule->updates as $update) {
                ScheduleUpdateRecorded::fire(
                    schedule_id: $schedule->id,
                    message: $update->message ?? '',
                    status: $update->status,
                    user_id: $update->user_id,
                );
            }

            if ($schedule->deleted_at) {
                ScheduleDeleted::fire(schedule_id: $schedule->id);
            }
        });
    }
};
