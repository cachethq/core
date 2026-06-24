<?php

use Cachet\Jobs\CheckComponent;
use Cachet\Models\Component;
use Illuminate\Support\Facades\Bus;

it('dispatches a check job for each eligible component', function () {
    Bus::fake();

    $eligible = Component::factory()->enabled()->checked()->count(2)->create();

    Component::factory()->disabled()->checked()->create();
    Component::factory()->enabled()->create(['checked' => false, 'link' => 'https://example.com']);
    Component::factory()->enabled()->create(['checked' => true, 'link' => null]);

    $this->artisan('cachet:check')->assertExitCode(0);

    Bus::assertDispatchedTimes(CheckComponent::class, 2);

    foreach ($eligible as $component) {
        Bus::assertDispatched(
            CheckComponent::class,
            fn (CheckComponent $job) => $job->component->is($component),
        );
    }
});

it('dispatches nothing when no components are eligible', function () {
    Bus::fake();

    Component::factory()->disabled()->checked()->create();

    $this->artisan('cachet:check')->assertExitCode(0);

    Bus::assertNotDispatched(CheckComponent::class);
});
