<?php

namespace Tests\Feature\Filament\Settings;

use Cachet\Enums\IncidentStatusEnum;
use Cachet\Filament\Pages\Settings\ManageRss;
use Cachet\Models\Incident;
use Cachet\Models\Schedule;
use Cachet\Settings\RssSettings;
use Filament\Facades\Filament;
use Workbench\App\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('cachet'));

    actingAs(User::factory()->create(['is_admin' => true]));
});

it('renders the manage rss page', function () {
    $this->get(ManageRss::getUrl())->assertOk();
});

it('saves the rss settings', function () {
    livewire(ManageRss::class)
        ->fillForm(['incident_fixed' => false, 'maintenance_complete' => false])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings = app(RssSettings::class)->refresh();

    expect($settings->incident_fixed)->toBeFalse()
        ->and($settings->maintenance_complete)->toBeFalse()
        ->and($settings->incident_watching)->toBeTrue();
});

it('leaves incidents out of the feed when their status is disabled', function () {
    $fixed = Incident::factory()->create(['status' => IncidentStatusEnum::fixed]);
    $open = Incident::factory()->create(['status' => IncidentStatusEnum::investigating]);

    $settings = app(RssSettings::class);
    $settings->incident_fixed = false;
    $settings->save();

    $this->get('/status/rss')
        ->assertOk()
        ->assertSee($open->name)
        ->assertDontSee($fixed->name);
});

it('leaves maintenance out of the feed when its state is disabled', function () {
    $upcoming = Schedule::factory()->create(['name' => 'Upcoming window zq1', 'scheduled_at' => now()->addDay(), 'completed_at' => null]);
    $complete = Schedule::factory()->create(['name' => 'Complete window zq2', 'scheduled_at' => now()->subDays(2), 'completed_at' => now()->subDay()]);

    $settings = app(RssSettings::class);
    $settings->maintenance_complete = false;
    $settings->save();

    $this->get('/status/rss/maintenance')
        ->assertOk()
        ->assertSee($upcoming->name)
        ->assertDontSee($complete->name);
});

it('returns an empty feed when every state is disabled', function () {
    Incident::factory()->create();

    $settings = app(RssSettings::class);
    foreach (IncidentStatusEnum::cases() as $status) {
        $settings->{'incident_'.$status->name} = false;
    }
    $settings->save();

    $xml = simplexml_load_string($this->get('/status/rss/incidents')->assertOk()->getContent());

    expect(count($xml->channel->item))->toBe(0);
});

it('limits the number of feed items', function () {
    Incident::factory()->count(3)->create();

    $settings = app(RssSettings::class);
    $settings->max_items = 2;
    $settings->save();

    $xml = simplexml_load_string($this->get('/status/rss/incidents')->assertOk()->getContent());

    expect(count($xml->channel->item))->toBe(2);
});

it('uses the refresh interval for the cache lifetime', function () {
    $settings = app(RssSettings::class);
    $settings->refresh_interval = 300;
    $settings->save();

    $this->get('/status/rss')->assertHeader('Cache-Control', 'max-age=300, public');
});

it('validates the feed limits', function () {
    livewire(ManageRss::class)
        ->fillForm(['max_items' => 0, 'refresh_interval' => 1])
        ->call('save')
        ->assertHasFormErrors(['max_items', 'refresh_interval']);
});
