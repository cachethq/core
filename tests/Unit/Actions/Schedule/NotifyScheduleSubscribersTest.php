<?php

use Cachet\Actions\Schedule\CreateSchedule;
use Cachet\Actions\Schedule\NotifyScheduleSubscribers;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\Schedule;
use Cachet\Models\Subscriber;
use Cachet\Notifications\NewScheduleNotification;
use Cachet\Settings\MailSettings;
use Cachet\Data\Requests\Schedule\CreateScheduleRequestData;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();

    $settings = app(MailSettings::class);
    $settings->allow_subscribers = true;
    $settings->save();
});

it('notifies verified subscribers about notifiable schedules', function () {
    $subscriber = Subscriber::factory()->verified()->create(['global' => true]);

    $schedule = Schedule::factory()->create(['notifications' => true]);

    app(NotifyScheduleSubscribers::class)->handle($schedule);

    Notification::assertSentTo($subscriber, NewScheduleNotification::class);
});

it('does not notify when the schedule does not want notifications', function () {
    Subscriber::factory()->verified()->create(['global' => true]);

    app(NotifyScheduleSubscribers::class)->handle(Schedule::factory()->create(['notifications' => false]));

    Notification::assertNothingSent();
});

it('does not notify when subscriptions are not allowed', function () {
    $settings = app(MailSettings::class);
    $settings->allow_subscribers = false;
    $settings->save();

    Subscriber::factory()->verified()->create(['global' => true]);

    app(NotifyScheduleSubscribers::class)->handle(Schedule::factory()->create(['notifications' => true]));

    Notification::assertNothingSent();
});

it('notifies component subscribers when a schedule is created through the create schedule action', function () {
    $component = Component::factory()->create();

    $subscriber = Subscriber::factory()->verified()->create(['global' => false]);
    $subscriber->components()->attach($component);

    app(CreateSchedule::class)->handle(CreateScheduleRequestData::from([
        'name' => 'Database maintenance',
        'message' => 'We will be upgrading the database.',
        'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
        'notifications' => true,
        'components' => [
            ['id' => $component->id, 'status' => ComponentStatusEnum::under_maintenance->value],
        ],
    ]));

    Notification::assertSentTo($subscriber, NewScheduleNotification::class);
});
