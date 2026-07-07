<?php

namespace Tests\Feature\Filament\Resources;

use Cachet\Filament\Resources\Incidents\Pages\CreateIncident;
use Cachet\Settings\MailSettings;
use Filament\Facades\Filament;
use Workbench\App\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('cachet'));

    actingAs(User::factory()->create(['is_admin' => true]));
});

it('hides the notify subscribers toggle when subscriptions are not allowed', function () {
    $this->get(CreateIncident::getUrl())
        ->assertOk()
        ->assertDontSee(__('cachet::incident.form.notifications_label'));
});

it('shows the notify subscribers toggle when subscriptions are allowed', function () {
    $settings = app(MailSettings::class);
    $settings->allow_subscribers = true;
    $settings->save();

    $this->get(CreateIncident::getUrl())
        ->assertOk()
        ->assertSee(__('cachet::incident.form.notifications_label'));
});
