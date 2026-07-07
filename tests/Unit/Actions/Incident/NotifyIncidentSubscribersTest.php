<?php

use Cachet\Actions\Incident\CreateIncident;
use Cachet\Actions\Incident\NotifyIncidentSubscribers;
use Cachet\Data\Requests\Incident\CreateIncidentRequestData;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\Component;
use Cachet\Models\Incident;
use Cachet\Models\Subscriber;
use Cachet\Notifications\NewIncidentNotification;
use Cachet\Settings\MailSettings;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();

    $settings = app(MailSettings::class);
    $settings->allow_subscribers = true;
    $settings->save();
});

function notifiableIncident(array $attributes = []): Incident
{
    return Incident::factory()->create(array_merge([
        'notifications' => true,
        'visible' => ResourceVisibilityEnum::guest,
    ], $attributes));
}

it('notifies verified global subscribers', function () {
    $subscriber = Subscriber::factory()->verified()->create(['global' => true]);

    app(NotifyIncidentSubscribers::class)->handle(notifiableIncident());

    Notification::assertSentTo($subscriber, NewIncidentNotification::class);
});

it('does not notify unverified subscribers', function () {
    $subscriber = Subscriber::factory()->create(['global' => true]);

    app(NotifyIncidentSubscribers::class)->handle(notifiableIncident());

    Notification::assertNotSentTo($subscriber, NewIncidentNotification::class);
});

it('notifies subscribers of an affected component but not others', function () {
    [$affected, $other] = Component::factory()->count(2)->create();

    $affectedSubscriber = Subscriber::factory()->verified()->create(['global' => false]);
    $affectedSubscriber->components()->attach($affected);

    $otherSubscriber = Subscriber::factory()->verified()->create(['global' => false]);
    $otherSubscriber->components()->attach($other);

    $incident = notifiableIncident();
    $incident->components()->attach($affected, ['component_status' => ComponentStatusEnum::major_outage]);

    app(NotifyIncidentSubscribers::class)->handle($incident);

    Notification::assertSentTo($affectedSubscriber, NewIncidentNotification::class);
    Notification::assertNotSentTo($otherSubscriber, NewIncidentNotification::class);
});

it('does not notify component subscribers when the incident has no components', function () {
    $subscriber = Subscriber::factory()->verified()->create(['global' => false]);
    $subscriber->components()->attach(Component::factory()->create());

    app(NotifyIncidentSubscribers::class)->handle(notifiableIncident());

    Notification::assertNothingSent();
});

it('does not notify when the incident does not want notifications', function () {
    Subscriber::factory()->verified()->create(['global' => true]);

    app(NotifyIncidentSubscribers::class)->handle(notifiableIncident(['notifications' => false]));

    Notification::assertNothingSent();
});

it('does not notify when the incident is not visible to guests', function () {
    Subscriber::factory()->verified()->create(['global' => true]);

    app(NotifyIncidentSubscribers::class)->handle(notifiableIncident(['visible' => ResourceVisibilityEnum::authenticated]));

    Notification::assertNothingSent();
});

it('notifies component subscribers when an incident is created through the create incident action', function () {
    $component = Component::factory()->create();

    $subscriber = Subscriber::factory()->verified()->create(['global' => false]);
    $subscriber->components()->attach($component);

    app(CreateIncident::class)->handle(CreateIncidentRequestData::from([
        'name' => 'My Incident',
        'message' => 'This is an incident message.',
        'status' => IncidentStatusEnum::investigating,
        'visible' => true,
        'notifications' => true,
        'components' => [
            ['id' => $component->id, 'status' => ComponentStatusEnum::major_outage->value],
        ],
    ]));

    Notification::assertSentTo($subscriber, NewIncidentNotification::class);
});

it('does not notify when subscriptions are not allowed', function () {
    $settings = app(MailSettings::class);
    $settings->allow_subscribers = false;
    $settings->save();

    Subscriber::factory()->verified()->create(['global' => true]);

    app(NotifyIncidentSubscribers::class)->handle(notifiableIncident());

    Notification::assertNothingSent();
});
