<?php

use Cachet\Models\Component;
use Cachet\Models\Schedule;
use Laravel\Sanctum\Sanctum;
use Workbench\App\User;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

it('can list schedules', function () {
    Schedule::factory(2)->create();

    $response = getJson('/status/api/schedules');

    $response->assertOk();
    $response->assertJsonCount(2, 'data');
});

it('does not list more than 15 schedules by default', function () {
    Schedule::factory(20)->create();

    $response = getJson('/status/api/schedules');

    $response->assertOk();
    $response->assertJsonCount(15, 'data');
});

it('can list more than 15 schedules', function () {
    Schedule::factory(20)->create();

    $response = getJson('/status/api/schedules?per_page=18');

    $response->assertOk();
    $response->assertJsonCount(18, 'data');
});

it('sorts schedules by id by default', function () {
    Schedule::factory(5)->create();

    $response = getJson('/status/api/schedules');

    $response->assertJsonPath('data.0.attributes.id', 1);
    $response->assertJsonPath('data.1.attributes.id', 2);
    $response->assertJsonPath('data.2.attributes.id', 3);
    $response->assertJsonPath('data.3.attributes.id', 4);
    $response->assertJsonPath('data.4.attributes.id', 5);
});

it('can sort schedules by name', function () {
    Schedule::factory(5)->sequence(
        ['name' => 'c'],
        ['name' => 'a'],
        ['name' => 'b'],
        ['name' => 'e'],
        ['name' => 'd'],
    )->create();

    $response = getJson('/status/api/schedules?sort=name');

    $response->assertJsonPath('data.0.attributes.id', 2);
    $response->assertJsonPath('data.1.attributes.id', 3);
    $response->assertJsonPath('data.2.attributes.id', 1);
    $response->assertJsonPath('data.3.attributes.id', 5);
    $response->assertJsonPath('data.4.attributes.id', 4);
});

it('can sort schedules by scheduled at date', function () {
    Schedule::factory(4)->sequence(
        ['scheduled_at' => '2022-01-01'],
        ['scheduled_at' => '2020-01-01'],
        ['scheduled_at' => '2023-01-01'],
        ['scheduled_at' => '2021-01-01'],
    )->create();

    $response = getJson('/status/api/schedules?sort=scheduled_at');

    $response->assertJsonPath('data.0.attributes.id', 2);
    $response->assertJsonPath('data.1.attributes.id', 4);
    $response->assertJsonPath('data.2.attributes.id', 1);
    $response->assertJsonPath('data.3.attributes.id', 3);
});

it('can sort schedules by completed at date', function () {
    Schedule::factory(4)->sequence(
        ['completed_at' => '2022-01-01'],
        ['completed_at' => '2020-01-01'],
        ['completed_at' => '2023-01-01'],
        ['completed_at' => '2021-01-01'],
    )->create();

    $response = getJson('/status/api/schedules?sort=completed_at');

    $response->assertJsonPath('data.0.attributes.id', 2);
    $response->assertJsonPath('data.1.attributes.id', 4);
    $response->assertJsonPath('data.2.attributes.id', 1);
    $response->assertJsonPath('data.3.attributes.id', 3);
});

it('can filter schedules by name', function () {
    Schedule::factory(20)->create();
    $schedule = Schedule::factory()->create([
        'name' => 'Name to filter by.',
    ]);

    $query = http_build_query([
        'filter' => [
            'name' => 'Name to filter by.',
        ],
    ]);

    $response = getJson('/status/api/schedules?'.$query);

    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.attributes.id', $schedule->id);
});

it('can filter schedules by status upcoming', function () {
    $schedule = Schedule::factory()->inTheFuture()->create();

    $query = http_build_query([
        'filter' => [
            'status' => \Cachet\Enums\ScheduleStatusEnum::upcoming,
        ],
    ]);

    $response = getJson('/status/api/schedules?'.$query);

    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.attributes.id', $schedule->id);
});

it('can filter schedules by status complete', function () {
    $schedule = Schedule::factory()->inThePast()->create();

    $query = http_build_query([
        'filter' => [
            'status' => \Cachet\Enums\ScheduleStatusEnum::complete,
        ],
    ]);

    $response = getJson('/status/api/schedules?'.$query);

    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.attributes.id', $schedule->id);
});

it('can filter schedules by status in progress', function () {
    $schedule = Schedule::factory()->inProgress()->create();

    $query = http_build_query([
        'filter' => [
            'status' => \Cachet\Enums\ScheduleStatusEnum::in_progress,
        ],
    ]);

    $response = getJson('/status/api/schedules?'.$query);

    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.attributes.id', $schedule->id);
});

it('can filter schedules by multiple statuses', function () {
    $scheduleCompleted = Schedule::factory()->completed()->create();
    $scheduleInProgress = Schedule::factory()->inProgress()->create();

    $query = http_build_query([
        'filter' => [
            'status' => implode(',', [
                \Cachet\Enums\ScheduleStatusEnum::in_progress->value,
                \Cachet\Enums\ScheduleStatusEnum::complete->value,
            ]
            ),
        ],
    ]);

    $response = getJson('/status/api/schedules?'.$query);

    $response->assertJsonCount(2, 'data');
    $response->assertJsonPath('data.0.attributes.id', $scheduleCompleted->id);
    $response->assertJsonPath('data.1.attributes.id', $scheduleInProgress->id);
});

it('can get a schedule', function () {
    $schedule = Schedule::factory()->create();

    $response = getJson('/status/api/schedules/'.$schedule->id);

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $schedule->id,
    ]);
});

it('cannot create a schedule if not authenticated', function () {
    $response = postJson('/status/api/schedules', [
        'name' => 'New Scheduled Maintenance',
        'message' => 'Something will go wrong.',
        'scheduled_at' => now()->addWeek()->toDateTimeString(),
        'completed_at' => now()->addWeek()->addDay()->toDateTimeString(),
    ]);

    $response->assertUnauthorized();
});

it('cannot create a schedule without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    $response = postJson('/status/api/schedules', [
        'name' => 'New Scheduled Maintenance',
        'message' => 'Something will go wrong.',
        'scheduled_at' => now()->addWeek()->toDateTimeString(),
        'completed_at' => now()->addWeek()->addDay()->toDateTimeString(),
    ]);

    $response->assertForbidden();
});

it('can create a schedule', function () {
    Sanctum::actingAs(User::factory()->create(), ['schedules.manage']);

    $response = postJson('/status/api/schedules', [
        'name' => 'New Scheduled Maintenance',
        'message' => 'Something will go wrong.',
        'scheduled_at' => $scheduleAt = now()->addWeek()->toDateTimeString(),
        'completed_at' => $completedAt = now()->addWeek()->addDay()->toDateTimeString(),
    ]);

    $response->assertCreated();
    $response->assertJson([
        'data' => [
            'attributes' => [
                'name' => 'New Scheduled Maintenance',
                'scheduled' => [
                    'string' => $scheduleAt,
                ],
                'completed' => [
                    'string' => $completedAt,
                ],
            ],
        ],
    ]);
    $this->assertDatabaseCount('schedule_components', 0);
});

it('can create a schedule with components', function () {
    Sanctum::actingAs(User::factory()->create(), ['schedules.manage']);

    [$componentA, $componentB] = Component::factory(2)->create();

    $response = postJson('/status/api/schedules', [
        'name' => 'New Scheduled Maintenance',
        'message' => 'Something will go wrong.',
        'scheduled_at' => $scheduleAt = now()->addWeek()->toDateTimeString(),
        'completed_at' => $completedAt = now()->addWeek()->addDay()->toDateTimeString(),
        'components' => [
            ['id' => $componentA->id, 'status' => 3],
            ['id' => $componentB->id, 'status' => 4],
        ],
    ]);

    $response->assertCreated();
    $response->assertJson([
        'data' => [
            'attributes' => [
                'name' => 'New Scheduled Maintenance',
                'scheduled' => [
                    'string' => $scheduleAt,
                ],
                'completed' => [
                    'string' => $completedAt,
                ],
            ],
        ],
    ]);
    $this->assertDatabaseCount('schedule_components', 2);
});

it('cannot create a schedule with bad data', function (array $payload) {
    Sanctum::actingAs(User::factory()->create(), ['schedules.manage']);

    $response = postJson('/status/api/schedules', $payload);

    $response->assertUnprocessable();
    $response->assertInvalid(array_keys($response->json('errors')));
})->with([
    fn () => ['name' => 'Missing Message', 'message' => null],
    fn () => ['name' => null, 'message' => 'Missing Name & Missing Scheduled At'],
    fn () => ['name' => 'Invalid Scheduled At', 'message' => 'Something', 'scheduled_at' => 'invalid'],
]);

it('cannot update a schedule if not authenticated', function () {
    $schedule = Schedule::factory()->create();

    $response = putJson('/status/api/schedules/'.$schedule->id, [
        'name' => 'Updated Scheduled Maintenance',
        'message' => 'Something went wrong.',
    ]);

    $response->assertUnauthorized();
});

it('cannot update a schedule without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    $schedule = Schedule::factory()->create();

    $response = putJson('/status/api/schedules/'.$schedule->id, [
        'name' => 'Updated Scheduled Maintenance',
        'message' => 'Something went wrong.',
    ]);

    $response->assertForbidden();
});

it('can update a schedule', function () {
    Sanctum::actingAs(User::factory()->create(), ['schedules.manage']);

    $schedule = Schedule::factory()->create();

    $response = putJson('/status/api/schedules/'.$schedule->id, [
        'name' => 'Updated Scheduled Maintenance',
        'message' => 'Something went wrong.',
    ]);

    $response->assertOk();
    $response->assertJson([
        'data' => [
            'attributes' => [
                'name' => 'Updated Scheduled Maintenance',
            ],
        ],
    ]);
});

it('can update a schedule with components', function () {
    Sanctum::actingAs(User::factory()->create(), ['schedules.manage']);

    [$componentA, $componentB] = Component::factory(2)->create();

    $schedule = Schedule::factory()->create();

    $response = putJson('/status/api/schedules/'.$schedule->id.'?include=components', [
        'name' => 'Updated Scheduled Maintenance',
        'message' => 'Something went wrong.',
        'components' => [
            ['id' => $componentA->id, 'status' => 3],
            ['id' => $componentB->id, 'status' => 4],
        ],
    ]);

    $response->assertOk();
    $response->assertJson([
        'data' => [
            'attributes' => [
                'name' => 'Updated Scheduled Maintenance',
            ],
        ],
    ]);
});

it('cannot delete a schedule if not authenticated', function () {
    $schedule = Schedule::factory()->create();

    $response = deleteJson('/status/api/schedules/'.$schedule->id);

    $response->assertUnauthorized();
});

it('cannot delete a schedule without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    $schedule = Schedule::factory()->create();

    $response = deleteJson('/status/api/schedules/'.$schedule->id);

    $response->assertForbidden();
});

it('can delete a schedule', function () {
    Sanctum::actingAs(User::factory()->create(), ['schedules.delete']);

    $schedule = Schedule::factory()->create();

    $response = deleteJson('/status/api/schedules/'.$schedule->id);

    $response->assertNoContent();
    $this->assertSoftDeleted('schedules', [
        'id' => $schedule->id,
    ]);
});
