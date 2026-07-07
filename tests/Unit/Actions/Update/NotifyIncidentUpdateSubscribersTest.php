<?php

use Cachet\Actions\Update\CreateUpdate;
use Cachet\Data\Requests\IncidentUpdate\CreateIncidentUpdateRequestData;
use Cachet\Data\Requests\ScheduleUpdate\CreateScheduleUpdateRequestData;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\Incident;
use Cachet\Models\Schedule;
use Cachet\Models\Subscriber;
use Cachet\Notifications\IncidentUpdatedNotification;
use Cachet\Notifications\ScheduleUpdatedNotification;
use Cachet\Settings\MailSettings;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();

    $settings = app(MailSettings::class);
    $settings->allow_subscribers = true;
    $settings->save();
});

it('notifies verified subscribers when an incident update is recorded', function () {
    $subscriber = Subscriber::factory()->verified()->create(['global' => true]);

    $incident = Incident::factory()->create([
        'notifications' => true,
        'visible' => ResourceVisibilityEnum::guest,
    ]);

    app(CreateUpdate::class)->handle($incident, CreateIncidentUpdateRequestData::from([
        'message' => 'We have identified the issue.',
        'status' => IncidentStatusEnum::identified,
    ]));

    Notification::assertSentTo($subscriber, IncidentUpdatedNotification::class);
});

it('does not notify when the incident does not want notifications', function () {
    Subscriber::factory()->verified()->create(['global' => true]);

    $incident = Incident::factory()->create([
        'notifications' => false,
        'visible' => ResourceVisibilityEnum::guest,
    ]);

    app(CreateUpdate::class)->handle($incident, CreateIncidentUpdateRequestData::from([
        'message' => 'We have identified the issue.',
        'status' => IncidentStatusEnum::identified,
    ]));

    Notification::assertNothingSent();
});

it('does not notify unverified subscribers', function () {
    Subscriber::factory()->create(['global' => true]);

    $incident = Incident::factory()->create([
        'notifications' => true,
        'visible' => ResourceVisibilityEnum::guest,
    ]);

    app(CreateUpdate::class)->handle($incident, CreateIncidentUpdateRequestData::from([
        'message' => 'We have identified the issue.',
        'status' => IncidentStatusEnum::identified,
    ]));

    Notification::assertNothingSent();
});

it('notifies subscribers about updates to notifiable schedules', function () {
    $subscriber = Subscriber::factory()->verified()->create(['global' => true]);

    $schedule = Schedule::factory()->create(['notifications' => true]);

    app(CreateUpdate::class)->handle($schedule, CreateScheduleUpdateRequestData::from([
        'message' => 'Maintenance is progressing.',
    ]));

    Notification::assertSentTo($subscriber, ScheduleUpdatedNotification::class);
});

it('does not notify about updates to schedules that do not want notifications', function () {
    Subscriber::factory()->verified()->create(['global' => true]);

    $schedule = Schedule::factory()->create(['notifications' => false]);

    app(CreateUpdate::class)->handle($schedule, CreateScheduleUpdateRequestData::from([
        'message' => 'Maintenance is progressing.',
    ]));

    Notification::assertNothingSent();
});
