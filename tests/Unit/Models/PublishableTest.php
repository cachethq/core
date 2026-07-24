<?php

use Cachet\Actions\Incident\CreateIncident;
use Cachet\Data\Requests\Incident\CreateIncidentRequestData;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\Incident;
use Cachet\Models\Schedule;
use Cachet\Models\Subscriber;
use Cachet\Settings\MailSettings;
use Illuminate\Support\Facades\Notification;

it('treats a null publish date as published', function () {
    $incident = Incident::factory()->create(['published_at' => null]);

    expect($incident->isPublished())->toBeTrue();
});

it('treats a past publish date as published', function () {
    $incident = Incident::factory()->published()->create();

    expect($incident->isPublished())->toBeTrue();
});

it('treats a future publish date as unpublished', function () {
    $incident = Incident::factory()->scheduled()->create();

    expect($incident->isPublished())->toBeFalse();
});

it('scopes incidents to published and unpublished', function () {
    Incident::factory()->create(['published_at' => null]);
    Incident::factory()->published()->create();
    Incident::factory()->scheduled()->create();

    expect(Incident::query()->published()->count())->toBe(2);
    expect(Incident::query()->unpublished()->count())->toBe(1);
});

it('scopes schedules to published and unpublished', function () {
    Schedule::factory()->create(['published_at' => null]);
    Schedule::factory()->scheduled()->create();

    expect(Schedule::query()->published()->count())->toBe(1);
    expect(Schedule::query()->unpublished()->count())->toBe(1);
});

it('does not notify subscribers when creating an incident scheduled for the future', function () {
    Notification::fake();

    $settings = app(MailSettings::class);
    $settings->allow_subscribers = true;
    $settings->save();

    $subscriber = Subscriber::factory()->verified()->create(['global' => true]);

    app(CreateIncident::class)->handle(CreateIncidentRequestData::from([
        'name' => 'Prescheduled outage',
        'status' => IncidentStatusEnum::investigating,
        'message' => 'Scheduled announcement.',
        'visible' => ResourceVisibilityEnum::guest->value,
        'notifications' => true,
        'published_at' => now()->addDay()->toDateTimeString(),
    ]));

    Notification::assertNothingSent();
});
