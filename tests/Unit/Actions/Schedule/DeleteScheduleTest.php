<?php

use Cachet\Actions\Schedule\DeleteSchedule;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\Schedule;
use Illuminate\Database\Eloquent\Relations\Relation;

it('can delete schedules', function () {
    $schedule = Schedule::factory()->create();

    app(DeleteSchedule::class)->handle($schedule);

    $this->assertSoftDeleted('schedules', [
        'id' => $schedule->id,
    ]);
});

it('keeps updates when the schedule is soft deleted', function () {
    $schedule = Schedule::factory()->hasUpdates(2)->create();

    app(DeleteSchedule::class)->handle($schedule);

    $this->assertSoftDeleted('schedules', [
        'id' => $schedule->id,
    ]);
    $this->assertDatabaseHas('updates', [
        'updateable_type' => Relation::getMorphAlias(Schedule::class),
        'updateable_id' => $schedule->id,
    ]);
});

it('purges updates and component attachments when the schedule is hard deleted', function () {
    $component = Component::factory()->create();
    $schedule = Schedule::factory()->hasUpdates(2)->create();
    $schedule->components()->attach($component->id, ['component_status' => ComponentStatusEnum::under_maintenance]);

    $schedule->forceDelete();

    $this->assertDatabaseMissing('updates', [
        'updateable_type' => Relation::getMorphAlias(Schedule::class),
        'updateable_id' => $schedule->id,
    ]);
    $this->assertDatabaseMissing('schedule_components', [
        'schedule_id' => $schedule->id,
    ]);
});
