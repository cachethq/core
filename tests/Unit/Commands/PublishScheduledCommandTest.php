<?php

use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\Incident;
use Cachet\Models\Schedule;
use Cachet\Models\Subscriber;
use Cachet\Notifications\NewIncidentNotification;
use Cachet\Notifications\NewScheduleNotification;
use Cachet\Settings\MailSettings;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();

    $settings = app(MailSettings::class);
    $settings->allow_subscribers = true;
    $settings->save();

    $this->subscriber = Subscriber::factory()->verified()->create(['global' => true]);
});

it('notifies subscribers when an incident reaches its publish time', function () {
    Incident::factory()->create([
        'notifications' => true,
        'visible' => ResourceVisibilityEnum::guest,
        'published_at' => now()->subMinute(),
    ]);

    $this->artisan('cachet:publish-scheduled')->assertSuccessful();

    Notification::assertSentTo($this->subscriber, NewIncidentNotification::class);
});

it('does not notify before an incident reaches its publish time', function () {
    Incident::factory()->create([
        'notifications' => true,
        'visible' => ResourceVisibilityEnum::guest,
        'published_at' => now()->addDay(),
    ]);

    $this->artisan('cachet:publish-scheduled')->assertSuccessful();

    Notification::assertNothingSent();
});

it('does not notify about the same published incident twice', function () {
    Incident::factory()->create([
        'notifications' => true,
        'visible' => ResourceVisibilityEnum::guest,
        'published_at' => now()->subMinute(),
    ]);

    $this->artisan('cachet:publish-scheduled')->assertSuccessful();
    $this->artisan('cachet:publish-scheduled')->assertSuccessful();

    Notification::assertSentToTimes($this->subscriber, NewIncidentNotification::class, 1);
});

it('records when the publish notification was sent', function () {
    $incident = Incident::factory()->create([
        'notifications' => true,
        'visible' => ResourceVisibilityEnum::guest,
        'published_at' => now()->subMinute(),
    ]);

    $this->artisan('cachet:publish-scheduled')->assertSuccessful();

    expect($incident->fresh()->published_notified_at)->not->toBeNull();
});

it('notifies subscribers when a maintenance reaches its publish time', function () {
    Schedule::factory()->create([
        'notifications' => true,
        'published_at' => now()->subMinute(),
    ]);

    $this->artisan('cachet:publish-scheduled')->assertSuccessful();

    Notification::assertSentTo($this->subscriber, NewScheduleNotification::class);
});

it('does not notify before a maintenance reaches its publish time', function () {
    Schedule::factory()->create([
        'notifications' => true,
        'published_at' => now()->addDay(),
    ]);

    $this->artisan('cachet:publish-scheduled')->assertSuccessful();

    Notification::assertNothingSent();
});

it('does not announce an incident that was published immediately on creation', function () {
    $incident = Incident::factory()->create([
        'notifications' => true,
        'visible' => ResourceVisibilityEnum::guest,
    ]);

    expect($incident->published_notified_at)->not->toBeNull();

    $this->artisan('cachet:publish-scheduled')->assertSuccessful();

    Notification::assertNothingSent();
});

it('does not announce a maintenance that was published immediately on creation', function () {
    $schedule = Schedule::factory()->create(['notifications' => true]);

    expect($schedule->published_notified_at)->not->toBeNull();

    $this->artisan('cachet:publish-scheduled')->assertSuccessful();

    Notification::assertNothingSent();
});

it('does nothing when subscriber notifications are disabled globally', function () {
    $settings = app(MailSettings::class);
    $settings->allow_subscribers = false;
    $settings->save();

    Incident::factory()->create([
        'notifications' => true,
        'visible' => ResourceVisibilityEnum::guest,
        'published_at' => now()->subMinute(),
    ]);

    $this->artisan('cachet:publish-scheduled')->assertSuccessful();

    Notification::assertNothingSent();
});
