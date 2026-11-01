<?php

namespace Tests\Feature\Filament\Resources;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Filament\Components\ComponentOptions;
use Cachet\Filament\Resources\Incidents\Pages\CreateIncident;
use Cachet\Filament\Resources\Incidents\Pages\EditIncident;
use Cachet\Filament\Resources\Incidents\Pages\ListIncidents;
use Cachet\Filament\Resources\Updates\RelationManagers\UpdatesRelationManager;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Cachet\Models\Incident;
use Cachet\Models\Subscriber;
use Cachet\Notifications\NewIncidentNotification;
use Cachet\Settings\MailSettings;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Notification;
use Workbench\App\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('cachet'));

    actingAs(User::factory()->create(['is_admin' => true]));
});

it('can create, reload, edit and clear a private note on a public incident', function () {
    livewire(CreateIncident::class)
        ->fillForm([
            'name' => 'Public outage',
            'status' => IncidentStatusEnum::investigating,
            'message' => 'We are investigating.',
            'visible' => ResourceVisibilityEnum::guest,
            'private_note' => 'Internal ticket SEC-482',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $incident = Incident::where('name', 'Public outage')->sole();

    expect($incident->private_note)->toBe('Internal ticket SEC-482')
        ->and($incident->visible)->toBe(ResourceVisibilityEnum::guest);

    livewire(EditIncident::class, ['record' => $incident->getKey()])
        ->assertFormSet(['private_note' => 'Internal ticket SEC-482'])
        ->fillForm(['private_note' => 'Escalated to the security team'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($incident->fresh()->private_note)->toBe('Escalated to the security team');

    livewire(EditIncident::class, ['record' => $incident->getKey()])
        ->assertFormSet(['private_note' => 'Escalated to the security team'])
        ->fillForm(['private_note' => null])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($incident->fresh()->private_note)->toBeNull();
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

it('sorts incidents by their canonical status column', function () {
    $fixed = Incident::factory()->create(['status' => IncidentStatusEnum::fixed]);
    $investigating = Incident::factory()->create(['status' => IncidentStatusEnum::investigating]);

    livewire(ListIncidents::class)
        ->sortTable('latest_status')
        ->assertCanSeeTableRecords([$investigating, $fixed], inOrder: true);
});

it('prioritizes unresolved incidents and then the most recent occurrence', function () {
    $recentFixed = Incident::factory()->create([
        'status' => IncidentStatusEnum::fixed,
        'occurred_at' => now(),
    ]);
    $olderInvestigating = Incident::factory()->create([
        'status' => IncidentStatusEnum::investigating,
        'occurred_at' => now()->subDay(),
    ]);
    $recentIdentified = Incident::factory()->create([
        'status' => IncidentStatusEnum::identified,
        'occurred_at' => now()->subHour(),
    ]);

    livewire(ListIncidents::class)
        ->assertCanSeeTableRecords([
            $recentIdentified,
            $olderInvestigating,
            $recentFixed,
        ], inOrder: true);
});

it('shows the update message as the primary update information', function () {
    $incident = Incident::factory()->create();
    $update = $incident->updates()->create([
        'message' => 'We have identified the source of the outage.',
        'status' => IncidentStatusEnum::identified,
        'user_id' => auth()->id(),
    ]);

    livewire(UpdatesRelationManager::class, [
        'ownerRecord' => $incident,
        'pageClass' => EditIncident::class,
    ])
        ->assertTableColumnExists('message')
        ->assertTableColumnStateSet('message', $update->message, $update);
});

it('excludes already attached components from the attach action options', function () {
    $incident = Incident::factory()->create();
    $attached = Component::factory()->create(['name' => 'Already Attached']);

    $incident->components()->attach($attached->id, ['component_status' => ComponentStatusEnum::operational->value]);

    $options = ComponentOptions::forSelect($incident);

    expect(collect($options)->flatten()->all())->not->toContain('Already Attached');
});

it('syncs the incident status when an update is deleted from the updates tab', function () {
    $incident = Incident::factory()->create(['status' => IncidentStatusEnum::fixed]);
    $incident->updates()->create([
        'message' => 'Cause identified.',
        'status' => IncidentStatusEnum::identified,
        'user_id' => auth()->id(),
        'created_at' => now()->subHour(),
    ]);
    $fixed = $incident->updates()->create([
        'message' => 'All fixed.',
        'status' => IncidentStatusEnum::fixed,
        'user_id' => auth()->id(),
    ]);

    livewire(UpdatesRelationManager::class, [
        'ownerRecord' => $incident,
        'pageClass' => EditIncident::class,
    ])
        ->callAction(TestAction::make('delete')->table($fixed));

    expect($incident->fresh()->status)->toBe(IncidentStatusEnum::identified);
});

it('syncs the incident status when updates are bulk deleted from the updates tab', function () {
    $incident = Incident::factory()->create(['status' => IncidentStatusEnum::fixed]);
    $incident->updates()->create([
        'message' => 'Cause identified.',
        'status' => IncidentStatusEnum::identified,
        'user_id' => auth()->id(),
        'created_at' => now()->subHour(),
    ]);
    $fixed = $incident->updates()->create([
        'message' => 'All fixed.',
        'status' => IncidentStatusEnum::fixed,
        'user_id' => auth()->id(),
    ]);

    livewire(UpdatesRelationManager::class, [
        'ownerRecord' => $incident,
        'pageClass' => EditIncident::class,
    ])
        ->selectTableRecords([$fixed])
        ->callAction(TestAction::make('delete')->table()->bulk());

    expect($incident->fresh()->status)->toBe(IncidentStatusEnum::identified);
});

it('syncs the incident status when an update status is edited from the updates tab', function () {
    $incident = Incident::factory()->create(['status' => IncidentStatusEnum::identified]);
    $update = $incident->updates()->create([
        'message' => 'Cause identified.',
        'status' => IncidentStatusEnum::identified,
        'user_id' => auth()->id(),
    ]);

    livewire(UpdatesRelationManager::class, [
        'ownerRecord' => $incident,
        'pageClass' => EditIncident::class,
    ])
        ->callAction(TestAction::make('edit')->table($update), data: [
            'status' => IncidentStatusEnum::fixed,
        ]);

    expect($incident->fresh()->status)->toBe(IncidentStatusEnum::fixed);
});
