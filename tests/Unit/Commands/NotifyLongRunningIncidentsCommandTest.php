<?php

use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Incident;
use Cachet\Models\Update;
use Cachet\Notifications\LongRunningIncidentNotification;
use Cachet\Settings\MailSettings;
use Illuminate\Support\Facades\Notification;
use Workbench\App\User;

beforeEach(function () {
    Notification::fake();

    $settings = app(MailSettings::class);
    $settings->notify_long_running_incidents = true;
    $settings->long_running_incident_hours = 6;
    $settings->save();

    $this->user = User::factory()->create();
});

function longRunningIncident(array $attributes = []): Incident
{
    return Incident::factory()->create(array_merge([
        'status' => IncidentStatusEnum::investigating,
        'created_at' => now()->subHours(7),
    ], $attributes));
}

it('notifies dashboard users about long-running incidents', function () {
    $incident = longRunningIncident();

    $this->artisan('cachet:notify-long-running-incidents')->assertSuccessful();

    Notification::assertSentTo($this->user, LongRunningIncidentNotification::class);

    expect($incident->fresh()->long_running_notified_at)->not->toBeNull();
});

it('does nothing when the setting is disabled', function () {
    $settings = app(MailSettings::class);
    $settings->notify_long_running_incidents = false;
    $settings->save();

    longRunningIncident();

    $this->artisan('cachet:notify-long-running-incidents')->assertSuccessful();

    Notification::assertNothingSent();
});

it('ignores incidents below the threshold', function () {
    longRunningIncident(['created_at' => now()->subHours(2)]);

    $this->artisan('cachet:notify-long-running-incidents')->assertSuccessful();

    Notification::assertNothingSent();
});

it('ignores resolved incidents', function () {
    longRunningIncident(['status' => IncidentStatusEnum::fixed]);

    $this->artisan('cachet:notify-long-running-incidents')->assertSuccessful();

    Notification::assertNothingSent();
});

it('resets the clock when an update is posted', function () {
    $incident = longRunningIncident();

    $incident->updates()->save(new Update([
        'message' => 'Still investigating.',
        'status' => IncidentStatusEnum::investigating,
    ]));

    $this->artisan('cachet:notify-long-running-incidents')->assertSuccessful();

    Notification::assertNothingSent();
});

it('does not notify about the same incident twice', function () {
    longRunningIncident();

    $this->artisan('cachet:notify-long-running-incidents')->assertSuccessful();
    $this->artisan('cachet:notify-long-running-incidents')->assertSuccessful();

    Notification::assertSentToTimes($this->user, LongRunningIncidentNotification::class, 1);
});

it('notifies again when the incident goes quiet after new activity', function () {
    $incident = longRunningIncident(['created_at' => now()->subHours(20)]);
    $incident->forceFill(['long_running_notified_at' => now()->subHours(14)])->saveQuietly();

    $update = new Update([
        'message' => 'A fix is being deployed.',
        'status' => IncidentStatusEnum::identified,
    ]);
    $update->created_at = now()->subHours(8);
    $incident->updates()->save($update);

    $this->artisan('cachet:notify-long-running-incidents')->assertSuccessful();

    Notification::assertSentTo($this->user, LongRunningIncidentNotification::class);
});
