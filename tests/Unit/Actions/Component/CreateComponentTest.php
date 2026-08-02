<?php

use Cachet\Actions\Component\CreateComponent;
use Cachet\Data\Requests\Component\CreateComponentRequestData;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Events\Components\ComponentCreated;
use Cachet\Models\Meta;
use Illuminate\Support\Facades\Event;

it('can create a component', function () {
    Event::fake();

    $data = CreateComponentRequestData::from([
        'name' => 'My Component',
        'description' => 'My component description',
    ]);

    $component = app(CreateComponent::class)->handle($data);

    expect($component)
        ->name->toBe($data->name)
        ->description->toBe($data->description)
        ->status->toBe(ComponentStatusEnum::unknown);

    Event::assertDispatched(ComponentCreated::class, fn ($event) => $event->component->is($component));
});

it('can create a component with a given status', function () {
    Event::fake();

    $data = CreateComponentRequestData::from([
        'name' => 'My Component',
        'description' => 'My component description',
        'status' => ComponentStatusEnum::performance_issues,
    ]);

    $component = app(CreateComponent::class)->handle($data);

    expect($component)
        ->name->toBe($data->name)
        ->description->toBe($data->description)
        ->status->toBe($data->status);

    Event::assertDispatched(ComponentCreated::class, fn ($event) => $event->component->is($component));
});

it('creates nothing when a write fails part-way', function () {
    Meta::creating(fn () => throw new RuntimeException('boom'));

    try {
        app(CreateComponent::class)->handle(CreateComponentRequestData::from([
            'name' => 'My Component',
            'meta' => ['cluster' => 'eu-west'],
        ]));
    } catch (RuntimeException) {
        //
    }

    $this->assertDatabaseCount('components', 0);
});
