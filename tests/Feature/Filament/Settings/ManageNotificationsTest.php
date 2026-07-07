<?php

namespace Tests\Feature\Filament\Settings;

use Cachet\Filament\Pages\Settings\ManageNotifications;
use Cachet\Filament\Resources\Subscribers\SubscriberResource;
use Cachet\Settings\MailSettings;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;
use Workbench\App\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('cachet'));

    actingAs(User::factory()->create(['is_admin' => true]));
});

it('renders the manage notifications page', function () {
    $this->get(ManageNotifications::getUrl())->assertOk();
});

it('saves mail settings', function () {
    livewire(ManageNotifications::class)
        ->fillForm([
            'mailer' => 'smtp',
            'host' => 'smtp.example.com',
            'port' => 2525,
            'username' => 'mailer@example.com',
            'password' => 'super-secret',
            'from_address' => 'status@example.com',
            'from_name' => 'Example Status',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings = app(MailSettings::class)->refresh();

    expect($settings->mailer)->toBe('smtp')
        ->and($settings->host)->toBe('smtp.example.com')
        ->and($settings->port)->toBe(2525)
        ->and($settings->username)->toBe('mailer@example.com')
        ->and($settings->password)->toBe('super-secret')
        ->and($settings->from_address)->toBe('status@example.com')
        ->and($settings->from_name)->toBe('Example Status');
});

it('saves the allow subscribers setting', function () {
    livewire(ManageNotifications::class)
        ->fillForm(['allow_subscribers' => true])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(app(MailSettings::class)->refresh()->allow_subscribers)->toBeTrue();
});

it('hides the subscribers resource from navigation until subscriptions are allowed', function () {
    expect(SubscriberResource::shouldRegisterNavigation())->toBeFalse();

    $settings = app(MailSettings::class);
    $settings->allow_subscribers = true;
    $settings->save();

    expect(SubscriberResource::shouldRegisterNavigation())->toBeTrue();
});

it('stores mail credentials encrypted at rest', function () {
    livewire(ManageNotifications::class)
        ->fillForm([
            'mailer' => 'smtp',
            'host' => 'smtp.example.com',
            'password' => 'super-secret',
            'from_address' => 'status@example.com',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $payload = DB::table('settings')
        ->where('group', 'mail')
        ->where('name', 'password')
        ->value('payload');

    expect($payload)->not->toContain('super-secret');
});

it('requires a host and from address when using smtp', function () {
    livewire(ManageNotifications::class)
        ->fillForm([
            'mailer' => 'smtp',
            'host' => null,
            'from_address' => null,
        ])
        ->call('save')
        ->assertHasFormErrors([
            'host' => 'required',
            'from_address' => 'required',
        ]);
});

it('sends a test email using the unsaved form state', function () {
    livewire(ManageNotifications::class)
        ->fillForm([
            'mailer' => 'log',
            'from_address' => 'status@example.com',
        ])
        ->call('sendTestEmail')
        ->assertNotified(__('cachet::settings.manage_notifications.test_email_sent', [
            'email' => auth()->user()->email,
        ]));
});

it('notifies when the test email cannot be sent', function () {
    livewire(ManageNotifications::class)
        ->fillForm([
            'mailer' => 'smtp',
            'host' => '127.0.0.1',
            'port' => 1,
            'from_address' => 'status@example.com',
        ])
        ->call('sendTestEmail')
        ->assertNotified(__('cachet::settings.manage_notifications.test_email_failed'));
});
