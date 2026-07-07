<?php

namespace Tests\Feature\StatusPage;

use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Schedule;
use Cachet\Models\Update;

use function Pest\Laravel\get;

it('renders the schedule page', function () {
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
        ->assertOk()
        ->assertSee('Database maintenance')
        ->assertSee('Maintenance window confirmed.');
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
