<?php

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\Schedule;
use Cachet\Settings\AppSettings;

it('renders the status page', function () {
    $this->get(route('cachet.status-page'))
        ->assertOk();
});

it('does not error when the from query parameter is malformed', function () {
    $this->get(route('cachet.status-page', ['from' => '2024-04-15/']))
        ->assertOk();
});

it('does not error when the from query parameter is not a date', function () {
    $this->get(route('cachet.status-page', ['from' => 'not-a-date']))
        ->assertOk();
});

it('shows upcoming and in progress maintenance in the maintenance block', function () {
    $upcoming = Schedule::factory()->inTheFuture()->create(['name' => 'Upcoming maintenance']);
    $inProgress = Schedule::factory()->inProgress()->create(['name' => 'In progress maintenance']);
    $completed = Schedule::factory()->inThePast()->create(['name' => 'Completed maintenance']);

    $response = $this->get(route('cachet.status-page'))->assertOk();

    $maintenanceBlock = $response->viewData('schedules');

    expect($maintenanceBlock->pluck('id'))
        ->toContain($upcoming->id, $inProgress->id)
        ->not->toContain($completed->id);
});

it('shows completed maintenance in the timeline instead of the maintenance block', function () {
    $completed = Schedule::factory()->completed()->create(['name' => 'Completed maintenance']);

    $response = $this->get(route('cachet.status-page'))->assertOk();

    expect($response->viewData('schedules')->pluck('id'))->not->toContain($completed->id);

    $response->assertSee('Completed maintenance');
});

it('does not render a dynamic favicon when the setting is disabled', function () {
    Component::factory()->create(['status' => ComponentStatusEnum::major_outage]);

    $this->get(route('cachet.status-page'))
        ->assertOk()
        ->assertDontSee('favicon-major-outage.svg');
});

it('renders the favicon for the current system status when dynamic favicons are enabled', function (array $componentStatuses, string $favicon) {
    $settings = app(AppSettings::class);
    $settings->dynamic_favicon = true;
    $settings->save();

    foreach ($componentStatuses as $componentStatus) {
        Component::factory()->create(['status' => $componentStatus]);
    }

    $this->get(route('cachet.status-page'))
        ->assertOk()
        ->assertSee($favicon);
})->with([
    'partial outage' => [[ComponentStatusEnum::operational, ComponentStatusEnum::partial_outage], 'favicon-partial-outage.svg'],
    'major outage' => [[ComponentStatusEnum::major_outage], 'favicon-major-outage.svg'],
    'under maintenance' => [[ComponentStatusEnum::under_maintenance], 'favicon-under-maintenance.svg'],
]);

it('falls back to the default favicon when operational and dynamic favicons are enabled', function () {
    $settings = app(AppSettings::class);
    $settings->dynamic_favicon = true;
    $settings->save();

    Component::factory()->create(['status' => ComponentStatusEnum::operational]);

    $this->get(route('cachet.status-page'))
        ->assertOk()
        ->assertSee('favicon.ico')
        ->assertDontSee('image/svg+xml');
});
