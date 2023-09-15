<?php

use Cachet\Models\Component;
use Cachet\Models\Schedule;

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

it('can sort schedules by status', function () {
    Schedule::factory(3)->sequence(
        ['status' => 1],
        ['status' => 2],
        ['status' => 0],
    )->create();

    $response = getJson('/status/api/schedules?sort=status');

    $response->assertJsonPath('data.0.attributes.id', 3);
    $response->assertJsonPath('data.1.attributes.id', 1);
    $response->assertJsonPath('data.2.attributes.id', 2);
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

it('can get a schedule', function () {
    $schedule = Schedule::factory()->create();

    $response = getJson('/status/api/schedules/'.$schedule->id);

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $schedule->id,
    ]);
});

it('can create a schedule', function () {
    $response = postJson('/status/api/schedules', [
        'name' => 'New Scheduled Maintenance',
        'message' => 'Something will go wrong.',
        'status' => 1,
        'scheduled_at' => $scheduleAt = now()->addWeek()->toDateTimeString(),
    ]);

    $response->assertCreated();
    $response->assertJson([
        'data' => [
            'attributes' => [
                'name' => 'New Scheduled Maintenance',
                'scheduled' => [
                    'string' => $scheduleAt,
                ],
            ],
        ],
    ]);
    $this->assertDatabaseCount('schedule_components', 0);
});

it('can create a schedule with components', function () {
    [$componentA, $componentB] = Component::factory(2)->create();

    $response = postJson('/status/api/schedules', [
        'name' => 'New Scheduled Maintenance',
        'message' => 'Something will go wrong.',
        'status' => 1,
        'scheduled_at' => $scheduleAt = now()->addWeek()->toDateTimeString(),
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
            ],
        ],
    ]);
    $this->assertDatabaseCount('schedule_components', 2);
});

it('cannot create a schedule with bad data', function (array $payload) {
    $response = postJson('/status/api/schedules', $payload);

    $response->assertUnprocessable();
    $response->assertInvalid(array_keys($response->json('errors')));
})->with([
    fn () => ['name' => 'Missing Message', 'message' => null],
    fn () => ['name' => null, 'message' => 'Missing Name & Invalid Status', 'status' => 999],
    fn () => ['name' => 'Invalid Scheduled At', 'message' => 'Something', 'status' => 1, 'scheduled_at' => 'invalid'],
]);

it('can update a schedule', function () {
    $schedule = Schedule::factory()->create();

    $response = putJson('/status/api/schedules/'.$schedule->id, [
        'name' => 'Updated Scheduled Maintenance',
        'message' => 'Something went wrong.',
        'status' => 2,
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
    [$componentA, $componentB] = Component::factory(2)->create();

    $schedule = Schedule::factory()->create();

    $response = putJson('/status/api/schedules/'.$schedule->id.'?include=components', [
        'name' => 'Updated Scheduled Maintenance',
        'message' => 'Something went wrong.',
        'status' => 2,
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

it('can update a schedule while passing null data', function (array $payload) {
    $schedule = Schedule::factory()->create();

    $response = putJson('/status/api/schedules/'.$schedule->id, $payload);

    $response->assertJson([
        'data' => [
            'attributes' => [
                'name' => 'Updated Incident',
                'status' => [
                    'value' => 2,
                ],
            ],
        ],
    ]);
})->with([
    fn () => ['name' => 'Updated Incident', 'status' => 2],
]);

it('can delete a schedule', function () {
    $schedule = Schedule::factory()->create();

    $response = deleteJson('/status/api/schedules/'.$schedule->id);

    $response->assertNoContent();
    $this->assertSoftDeleted('schedules', [
        'id' => $schedule->id,
    ]);
});
