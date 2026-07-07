<?php

use Cachet\Actions\Update\CreateUpdate;
use Cachet\Data\Requests\ScheduleUpdate\CreateScheduleUpdateRequestData;
use Cachet\Models\Schedule;
use Cachet\Models\Subscriber;
use Cachet\Notifications\ScheduleCompletedNotification;
use Cachet\Notifications\ScheduleRescheduledNotification;
use Cachet\Notifications\ScheduleUpdatedNotification;
use Cachet\Settings\MailSettings;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();

    $settings = app(MailSettings::class);
    $settings->allow_subscribers = true;
    $settings->save();

    $this->subscriber = Subscriber::factory()->verified()->create(['global' => true]);
});

it('notifies subscribers when the maintenance start time moves', function () {
    $previous = now()->addDay()->startOfMinute();

    $schedule = Schedule::factory()->create([
        'notifications' => true,
        'scheduled_at' => $previous,
    ]);

    $schedule->update(['scheduled_at' => now()->addDays(2)]);

    Notification::assertSentTo(
        $this->subscriber,
        ScheduleRescheduledNotification::class,
        fn (ScheduleRescheduledNotification $notification) => $notification->previousScheduledAt->eq($previous),
    );
});

it('notifies subscribers when the maintenance window is extended', function () {
    $schedule = Schedule::factory()->create([
        'notifications' => true,
        'scheduled_at' => now()->subHour(),
        'completed_at' => now()->addHour(),
    ]);

    $schedule->update(['completed_at' => now()->addHours(3)]);

    Notification::assertSentTo($this->subscriber, ScheduleRescheduledNotification::class);
});

it('does not notify for content-only edits', function () {
    $schedule = Schedule::factory()->create([
        'notifications' => true,
        'scheduled_at' => now()->addDay(),
    ]);

    $schedule->update(['name' => 'Renamed maintenance', 'message' => 'Clarified copy.']);

    Notification::assertNothingSent();
});

it('sends the completion notification instead when the change completes the maintenance', function () {
    $schedule = Schedule::factory()->create([
        'notifications' => true,
        'scheduled_at' => now()->subHours(2),
    ]);

    $schedule->update(['completed_at' => now()->subMinute()]);

    Notification::assertSentTo($this->subscriber, ScheduleCompletedNotification::class);
    Notification::assertNotSentTo($this->subscriber, ScheduleRescheduledNotification::class);
});

it('does not notify when a recorded update extends the window', function () {
    $schedule = Schedule::factory()->create([
        'notifications' => true,
        'scheduled_at' => now()->subHour(),
        'completed_at' => now()->addHour(),
    ]);

    app(CreateUpdate::class)->handle($schedule, CreateScheduleUpdateRequestData::from([
        'message' => 'We need a little more time.',
        'completed_at' => now()->addHours(3)->format('Y-m-d H:i:s'),
    ]));

    Notification::assertSentTo($this->subscriber, ScheduleUpdatedNotification::class);
    Notification::assertNotSentTo($this->subscriber, ScheduleRescheduledNotification::class);
});

it('does not notify for edits to completed maintenance', function () {
    $schedule = Schedule::factory()->create([
        'notifications' => true,
        'scheduled_at' => now()->subHours(6),
    ]);

    $schedule->forceFill([
        'completed_at' => now()->subHours(2),
        'completed_notified_at' => now()->subHours(2),
    ])->saveQuietly();

    $schedule->update(['scheduled_at' => now()->subHours(5)]);

    Notification::assertNothingSent();
});

it('does not notify when the schedule does not want notifications', function () {
    $schedule = Schedule::factory()->create([
        'notifications' => false,
        'scheduled_at' => now()->addDay(),
    ]);

    $schedule->update(['scheduled_at' => now()->addDays(2)]);

    Notification::assertNothingSent();
});
