<?php

use Cachet\Actions\Component\ChangeComponentStatus;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\ComponentStatusSourceEnum;
use Cachet\Events\Components\ComponentStatusWasChanged;
use Cachet\Models\Component;
use Illuminate\Support\Facades\Event;
use Workbench\App\User;

it('records the change with its source and announces it', function () {
    Event::fake([ComponentStatusWasChanged::class]);

    $component = Component::factory()->create(['status' => ComponentStatusEnum::operational]);

    app(ChangeComponentStatus::class)->handle(
        $component,
        ComponentStatusEnum::major_outage,
        ComponentStatusSourceEnum::Monitor,
        reason: 'Connection refused',
    );

    expect($component->fresh()->status)->toBe(ComponentStatusEnum::major_outage);

    $change = $component->statusChanges()->sole();

    expect($change)
        ->old_status->toBe(ComponentStatusEnum::operational)
        ->new_status->toBe(ComponentStatusEnum::major_outage)
        ->source->toBe(ComponentStatusSourceEnum::Monitor)
        ->reason->toBe('Connection refused');

    Event::assertDispatched(
        ComponentStatusWasChanged::class,
        fn (ComponentStatusWasChanged $event) => $event->source === ComponentStatusSourceEnum::Monitor
            && $event->oldStatus === ComponentStatusEnum::operational
            && $event->newStatus === ComponentStatusEnum::major_outage,
    );
});

it('attributes the change to whoever caused it', function () {
    $user = User::factory()->create();
    $component = Component::factory()->create(['status' => ComponentStatusEnum::operational]);

    app(ChangeComponentStatus::class)->handle(
        $component,
        ComponentStatusEnum::partial_outage,
        ComponentStatusSourceEnum::Manual,
        $user,
    );

    expect($component->statusChanges()->sole()->causer->is($user))->toBeTrue();
});

it('does nothing when the status is unchanged', function () {
    Event::fake([ComponentStatusWasChanged::class]);

    $component = Component::factory()->create(['status' => ComponentStatusEnum::operational]);

    app(ChangeComponentStatus::class)->handle($component, ComponentStatusEnum::operational);

    expect($component->statusChanges()->count())->toBe(0);

    Event::assertNotDispatched(ComponentStatusWasChanged::class);
});
