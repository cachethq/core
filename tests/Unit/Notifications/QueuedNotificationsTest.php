<?php

namespace Tests\Unit\Notifications;

use Cachet\Notifications\IncidentUpdatedNotification;
use Cachet\Notifications\LongRunningIncidentNotification;
use Cachet\Notifications\NewIncidentNotification;
use Cachet\Notifications\NewScheduleNotification;
use Cachet\Notifications\ScheduleCompletedNotification;
use Cachet\Notifications\ScheduleRescheduledNotification;
use Cachet\Notifications\ScheduleUpdatedNotification;
use Cachet\Notifications\VerifySubscriberEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use ReflectionClass;

it('discards queued notifications whose models no longer exist', function (string $notification) {
    $reflection = new ReflectionClass($notification);

    expect($reflection->implementsInterface(ShouldQueue::class))->toBeTrue()
        ->and($reflection->getDefaultProperties()['deleteWhenMissingModels'])->toBeTrue();
})->with([
    VerifySubscriberEmail::class,
    NewIncidentNotification::class,
    IncidentUpdatedNotification::class,
    LongRunningIncidentNotification::class,
    NewScheduleNotification::class,
    ScheduleUpdatedNotification::class,
    ScheduleRescheduledNotification::class,
    ScheduleCompletedNotification::class,
]);
