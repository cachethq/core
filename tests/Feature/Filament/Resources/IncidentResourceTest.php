<?php

namespace Tests\Feature\Filament\Resources;

use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Filament\Resources\Incidents\Pages\CreateIncident;
use Cachet\Filament\Resources\Incidents\Pages\EditIncident;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Cachet\Models\Incident;
use Cachet\Models\Subscriber;
use Cachet\Notifications\NewIncidentNotification;
use Cachet\Settings\MailSettings;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Notification;
use Workbench\App\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('cachet'));

    actingAs(User::factory()->create(['is_admin' => true]));
});

it('hides the notify subscribers toggle when subscriptions are not allowed', function () {
    $this->get(CreateIncident::getUrl())
        ->assertOk()
        ->assertDontSee(__('cachet::incident.form.notify_subscribers_label'));
});

it('shows the notify subscribers toggle when subscriptions are allowed', function () {
    $settings = app(MailSettings::class);
    $settings->allow_subscribers = true;
    $settings->save();

    $this->get(CreateIncident::getUrl())
        ->assertOk()
        ->assertSee(__('cachet::incident.form.notify_subscribers_label'));
});

it('defaults the incident update user to the incident reporter', function () {
    $reporter = User::factory()->create(['is_admin' => true]);
    $incident = Incident::factory()->create(['user_id' => $reporter->id]);

    livewire(EditIncident::class, ['record' => $incident->getKey()])
        ->mountAction('add-update')
        ->assertActionDataSet(['user_id' => $reporter->id]);
});

it('notifies subscribers when creating an incident from the dashboard', function () {
    Notification::fake();

    $settings = app(MailSettings::class);
    $settings->allow_subscribers = true;
    $settings->save();

    $subscriber = Subscriber::factory()->verified()->create(['global' => true]);

    livewire(CreateIncident::class)
        ->fillForm([
            'name' => 'Dashboard Incident',
            'status' => IncidentStatusEnum::investigating,
            'message' => 'Something broke.',
            'visible' => ResourceVisibilityEnum::guest,
            'notifications' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    Notification::assertSentTo($subscriber, NewIncidentNotification::class);
});

it('groups component selections by component group when creating an incident', function () {
    $firstGroup = ComponentGroup::factory()->create(['name' => 'First Group', 'order' => 1]);
    $secondGroup = ComponentGroup::factory()->create(['name' => 'Second Group', 'order' => 2]);

    Component::factory()->for($firstGroup, 'group')->create(['name' => 'Webservice']);
    Component::factory()->for($secondGroup, 'group')->create(['name' => 'Webservice']);
    Component::factory()->create(['name' => 'Ungrouped Service']);

    livewire(CreateIncident::class)
        ->fillForm([
            'incidentComponents' => [['component_id' => null]],
        ])
        ->assertSeeInOrder(['First Group', 'Second Group', 'Ungrouped components'])
        ->assertSee('Webservice')
        ->assertSee('Ungrouped Service');
});
