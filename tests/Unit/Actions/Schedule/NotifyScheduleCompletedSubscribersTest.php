<?php

use Cachet\Actions\Update\CreateUpdate;
use Cachet\Data\Requests\ScheduleUpdate\CreateScheduleUpdateRequestData;
use Cachet\Models\Schedule;
use Cachet\Models\Subscriber;
use Cachet\Notifications\ScheduleCompletedNotification;
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

it('notifies subscribers when maintenance is completed', function () {
    $schedule = Schedule::factory()->create([
        'notifications' => true,
        'scheduled_at' => now()->subHours(2),
    ]);

    $schedule->update(['completed_at' => now()->subMinute()]);

    Notification::assertSentTo($this->subscriber, ScheduleCompletedNotification::class);
});

it('does not notify completion when the completion time is in the future', function () {
    $schedule = Schedule::factory()->create([
        'notifications' => true,
        'scheduled_at' => now()->subHours(2),
    ]);

    $schedule->update(['completed_at' => now()->addHour()]);

    Notification::assertNotSentTo($this->subscriber, ScheduleCompletedNotification::class);
});

it('does not notify when the schedule does not want notifications', function () {
    $schedule = Schedule::factory()->create([
        'notifications' => false,
        'scheduled_at' => now()->subHours(2),
    ]);

    $schedule->update(['completed_at' => now()->subMinute()]);

    Notification::assertNothingSent();
});

it('sends the completion notification instead of the update notification when an update completes the maintenance', function () {
    $schedule = Schedule::factory()->create([
        'notifications' => true,
        'scheduled_at' => now()->subHours(2),
    ]);

    app(CreateUpdate::class)->handle($schedule, CreateScheduleUpdateRequestData::from([
        'message' => 'Maintenance is complete.',
        'completed_at' => now()->subMinute()->format('Y-m-d H:i:s'),
    ]));

    Notification::assertSentTo($this->subscriber, ScheduleCompletedNotification::class);
    Notification::assertNotSentTo($this->subscriber, ScheduleUpdatedNotification::class);
});
