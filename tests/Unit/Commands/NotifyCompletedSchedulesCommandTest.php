<?php

use Cachet\Models\Schedule;
use Cachet\Models\Subscriber;
use Cachet\Notifications\ScheduleCompletedNotification;
use Cachet\Settings\MailSettings;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();

    $settings = app(MailSettings::class);
    $settings->allow_subscribers = true;
    $settings->save();

    $this->subscriber = Subscriber::factory()->verified()->create(['global' => true]);
});

it('notifies subscribers when a future-dated completion time passes', function () {
    Schedule::factory()->create([
        'notifications' => true,
        'scheduled_at' => now()->subHours(3),
        'completed_at' => now()->subMinutes(2),
    ]);

    $this->artisan('cachet:notify-completed-schedules')->assertSuccessful();

    Notification::assertSentTo($this->subscriber, ScheduleCompletedNotification::class);
});

it('does not notify before the completion time passes', function () {
    Schedule::factory()->create([
        'notifications' => true,
        'scheduled_at' => now()->subHour(),
        'completed_at' => now()->addHour(),
    ]);

    $this->artisan('cachet:notify-completed-schedules')->assertSuccessful();

    Notification::assertNothingSent();
});

it('does not notify about the same completion twice', function () {
    Schedule::factory()->create([
        'notifications' => true,
        'scheduled_at' => now()->subHours(3),
        'completed_at' => now()->subMinutes(2),
    ]);

    $this->artisan('cachet:notify-completed-schedules')->assertSuccessful();
    $this->artisan('cachet:notify-completed-schedules')->assertSuccessful();

    Notification::assertSentToTimes($this->subscriber, ScheduleCompletedNotification::class, 1);
});

it('does not notify again through the command after an immediate completion already notified', function () {
    $schedule = Schedule::factory()->create([
        'notifications' => true,
        'scheduled_at' => now()->subHours(3),
    ]);

    $schedule->update(['completed_at' => now()->subMinute()]);

    $this->artisan('cachet:notify-completed-schedules')->assertSuccessful();

    Notification::assertSentToTimes($this->subscriber, ScheduleCompletedNotification::class, 1);
});

it('notifies again when the maintenance window is extended past a previous notification', function () {
    $schedule = Schedule::factory()->create([
        'notifications' => true,
        'scheduled_at' => now()->subHours(6),
    ]);

    $schedule->forceFill([
        'completed_at' => now()->subMinutes(5),
        'completed_notified_at' => now()->subHours(2),
    ])->saveQuietly();

    $this->artisan('cachet:notify-completed-schedules')->assertSuccessful();

    Notification::assertSentTo($this->subscriber, ScheduleCompletedNotification::class);
});

it('does not notify schedules that do not want notifications', function () {
    Schedule::factory()->create([
        'notifications' => false,
        'scheduled_at' => now()->subHours(3),
        'completed_at' => now()->subMinutes(2),
    ]);

    $this->artisan('cachet:notify-completed-schedules')->assertSuccessful();

    Notification::assertNothingSent();
});
