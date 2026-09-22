<?php

namespace Tests\Feature\StatusPage;

use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Schedule;
use Cachet\Models\Update;

use function Pest\Laravel\get;

it('renders schedule details with stable theme selectors', function () {
    $schedule = Schedule::factory()->create([
        'name' => 'Database maintenance',
        'message' => 'We will be upgrading the database.',
        'scheduled_at' => now()->addDay(),
    ]);

    $update = new Update([
        'message' => 'Maintenance window confirmed.',
        'status' => IncidentStatusEnum::unknown,
    ]);
    $schedule->updates()->save($update);

    get(route('cachet.status-page.schedule', ['schedule' => $schedule]))
        ->assertSee('Database maintenance')
        ->assertSee('Maintenance window confirmed.')
        ->assertSee([
            'data-page="schedule"',
            'data-component="schedule"',
            'data-component="schedule-update"',
            'data-component="badge"',
            'data-component="timestamp"',
            'data-component="page-navigation"',
            'data-slot="main"',
            'data-slot="message"',
        ], escape: false);
});

it('links to schedules from the status page', function () {
    $schedule = Schedule::factory()->create(['scheduled_at' => now()->addDay()]);

    get(route('cachet.status-page'))
        ->assertOk()
        ->assertSee(route('cachet.status-page.schedule', ['schedule' => $schedule]));
});

it('returns not found for a missing schedule', function () {
    get(route('cachet.status-page.schedule', ['schedule' => 999]))->assertNotFound();
});
