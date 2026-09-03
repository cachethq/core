<?php

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Facades\CachetView;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Cachet\Models\Incident;
use Cachet\Models\Metric;
use Cachet\Models\Schedule;
use Cachet\Settings\AppSettings;
use Cachet\View\RenderHook;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

it('renders the status page', function () {
    $this->get(route('cachet.status-page'))
        ->assertOk();
});

it('renders an SVG badge for the overall status page status', function () {
    Component::factory()->create(['status' => ComponentStatusEnum::operational]);

    $response = $this->get(route('cachet.status-page.badge'))
        ->assertOk()
        ->assertHeader('content-type', 'image/svg+xml; charset=UTF-8')
        ->assertSee('<svg', escape: false)
        ->assertSee('All systems are operational.');

    expect($response->headers->get('cache-control'))
        ->toContain('public')
        ->toContain('max-age=60')
        ->toContain('s-maxage=60');
});

it('renders an SVG badge for a public component', function () {
    $component = Component::factory()->create([
        'name' => 'Public API',
        'status' => ComponentStatusEnum::major_outage,
    ]);

    $this->get(route('cachet.status-page.component.badge', $component))
        ->assertOk()
        ->assertHeader('content-type', 'image/svg+xml; charset=UTF-8')
        ->assertSee('<svg', escape: false)
        ->assertSee('Public API')
        ->assertSee('Major outage');
});

it('does not render a badge for a component in a private group', function () {
    $group = ComponentGroup::factory()->create(['visible' => 0]);
    $component = Component::factory()->create(['component_group_id' => $group->id]);

    $this->get(route('cachet.status-page.component.badge', $component))
        ->assertNotFound();
});

it('does not render a badge for a disabled component', function () {
    $component = Component::factory()->create(['enabled' => false]);

    $this->get(route('cachet.status-page.component.badge', $component))
        ->assertNotFound();
});

it('can hide the site name and about content without changing status page metadata', function () {
    $settings = app(AppSettings::class);
    $settings->name = 'Acme Status';
    $settings->about = 'A private production system.';
    $settings->show_site_name = false;
    $settings->show_about = false;
    $settings->save();

    $response = $this->get(route('cachet.status-page'))->assertOk();
    $body = Str::after($response->getContent(), '<body');

    expect($body)
        ->not->toContain('Acme Status')
        ->not->toContain('A private production system.');

    $response
        ->assertSee('<title>Acme Status</title>', escape: false)
        ->assertSee('<meta name="description" content="A private production system." />', escape: false);
});

it('shows the site name once when it is enabled', function () {
    $settings = app(AppSettings::class);
    $settings->name = 'Acme Status';
    $settings->show_site_name = true;
    $settings->show_about = false;
    $settings->save();

    $response = $this->get(route('cachet.status-page'))->assertOk();
    $body = Str::after($response->getContent(), '<body');

    expect(substr_count($body, 'Acme Status'))->toBe(1);
});

it('uses a logical heading hierarchy for components', function () {
    $group = ComponentGroup::factory()->create(['name' => 'Core services']);
    Component::factory()->create(['name' => 'Public API']);
    Component::factory()->create(['name' => 'Core API', 'component_group_id' => $group->id]);

    $page = $this->get(route('cachet.status-page'))
        ->assertOk()
        ->getContent();

    expect($page)
        ->toMatch('/<h2[^>]*>\s*Core services\s*<\\/h2>/')
        ->toMatch('/<h3[^>]*>\s*Core API\s*<\\/h3>/')
        ->toMatch('/<h2[^>]*>\s*Public API\s*<\\/h2>/');
});

it('renders the components after hook after each component', function () {
    Component::factory()->create(['name' => 'Public API']);
    CachetView::registerRenderHook(RenderHook::STATUS_PAGE_COMPONENTS_AFTER, fn () => '<span>components-after-hook</span>');

    $this->get(route('cachet.status-page'))
        ->assertOk()
        ->assertSee('components-after-hook');
});

it('renders the declared banner metrics and footer hooks', function () {
    Metric::factory()->create([
        'visible' => ResourceVisibilityEnum::guest,
        'display_chart' => true,
        'show_when_empty' => true,
    ]);
    Cache::forget('cachet::metrics.guests');

    CachetView::registerRenderHook(RenderHook::STATUS_PAGE_BANNER, fn () => '<span>banner-hook</span>');
    CachetView::registerRenderHook(RenderHook::STATUS_PAGE_METRICS_BEFORE, fn () => '<span>metrics-before-hook</span>');
    CachetView::registerRenderHook(RenderHook::STATUS_PAGE_METRICS_AFTER, fn () => '<span>metrics-after-hook</span>');
    CachetView::registerRenderHook(RenderHook::FOOTER, fn () => '<span>footer-hook</span>');

    $this->get(route('cachet.status-page'))
        ->assertOk()
        ->assertSeeInOrder([
            'data-component="header"',
            'banner-hook',
            'metrics-before-hook',
            'data-component="metrics"',
            'metrics-after-hook',
            'data-component="footer"',
            'footer-hook',
        ], escape: false);
});

it('renders a footer hook without built-in footer content', function () {
    $settings = app(AppSettings::class);
    $settings->show_support = false;
    $settings->show_timezone = false;
    $settings->save();

    CachetView::registerRenderHook(RenderHook::FOOTER, fn () => '<span>footer-hook</span>');

    $this->get(route('cachet.status-page'))
        ->assertOk()
        ->assertSee('data-component="footer"', escape: false)
        ->assertSee('footer-hook');
});

it('renders status summary and incident timeline hooks', function () {
    CachetView::registerRenderHook(RenderHook::STATUS_PAGE_STATUS_SUMMARY_BEFORE, fn () => '<span>summary-before-hook</span>');
    CachetView::registerRenderHook(RenderHook::STATUS_PAGE_STATUS_SUMMARY_AFTER, fn () => '<span>summary-after-hook</span>');
    CachetView::registerRenderHook(RenderHook::STATUS_PAGE_INCIDENT_TIMELINE_BEFORE, fn () => '<span>timeline-before-hook</span>');
    CachetView::registerRenderHook(RenderHook::STATUS_PAGE_INCIDENT_TIMELINE_AFTER, fn () => '<span>timeline-after-hook</span>');

    $this->get(route('cachet.status-page'))
        ->assertOk()
        ->assertSeeInOrder([
            'summary-before-hook',
            'data-component="status-summary"',
            'summary-after-hook',
            'timeline-before-hook',
            'data-component="incident-timeline"',
            'timeline-after-hook',
        ], escape: false);
});

it('renders stable theme attributes on the status page', function () {
    $settings = app(AppSettings::class);
    $settings->about = 'Service status and uptime.';
    $settings->show_about = true;
    $settings->save();

    $group = ComponentGroup::factory()->create();
    $component = Component::factory()->create(['component_group_id' => $group->id]);
    $metric = Metric::factory()->create([
        'visible' => ResourceVisibilityEnum::guest,
        'display_chart' => true,
        'show_when_empty' => true,
    ]);
    $schedule = Schedule::factory()->inTheFuture()->create();
    $incident = Incident::factory()->create();
    Cache::forget('cachet::metrics.guests');

    $this->get(route('cachet.status-page'))
        ->assertOk()
        ->assertSee('data-page="status"', escape: false)
        ->assertSee('data-component="header"', escape: false)
        ->assertSee('data-component="status-overview"', escape: false)
        ->assertSee('data-component="status-summary"', escape: false)
        ->assertSee('data-component="component-list"', escape: false)
        ->assertSee('data-component="component-group"', escape: false)
        ->assertSee('data-component="component"', escape: false)
        ->assertSee('data-component="about"', escape: false)
        ->assertSee('data-component="metrics"', escape: false)
        ->assertSee('data-component="metric"', escape: false)
        ->assertSee('data-component="schedules"', escape: false)
        ->assertSee('data-component="schedule"', escape: false)
        ->assertSee('data-component="incident-timeline"', escape: false)
        ->assertSee('data-component="incident-day"', escape: false)
        ->assertSee('data-component="incident"', escape: false)
        ->assertSee('data-component="incident-update"', escape: false)
        ->assertSee('data-component="incident-update-status"', escape: false)
        ->assertSee('data-component="badge"', escape: false)
        ->assertSee('data-component="timestamp"', escape: false)
        ->assertSee('data-component="logo"', escape: false)
        ->assertSee('data-component="footer"', escape: false)
        ->assertSee('data-component-group-id="'.$group->getKey().'"', escape: false)
        ->assertSee('data-component-id="'.$component->getKey().'"', escape: false)
        ->assertSee('data-metric-id="'.$metric->getKey().'"', escape: false)
        ->assertSee('data-schedule-id="'.$schedule->getKey().'"', escape: false)
        ->assertSee('data-incident-id="'.$incident->getKey().'"', escape: false)
        ->assertSee('data-update-id="reported"', escape: false)
        ->assertSee('data-slot="main"', escape: false)
        ->assertSee('data-slot="title"', escape: false)
        ->assertSee('data-slot="status"', escape: false)
        ->assertSee('data-slot="indicator"', escape: false)
        ->assertSee('data-slot="content"', escape: false);
});

it('gives status page controls accessible names', function () {
    Component::factory()->create([
        'name' => 'Public API',
        'description' => 'The public API.',
    ]);

    $page = $this->get(route('cachet.status-page'))
        ->assertOk()
        ->assertSee('aria-label="'.__('cachet::component.description_label', ['component' => 'Public API']).'"', escape: false)
        ->assertSee('aria-label="'.__('cachet::incident.timeline.date_range_label').'"', escape: false)
        ->getContent();

    expect($page)
        ->toMatch('/<label[^>]*>.*'.__('cachet::incident.timeline.from_label').'.*<input[^>]*type="date"/s')
        ->toMatch('/<label[^>]*>.*'.__('cachet::incident.timeline.to_label').'.*<input[^>]*type="date"/s');
});

it('marks the page for conditional metrics loading only when a metric chart exists', function () {
    $this->get(route('cachet.status-page'))
        ->assertOk()
        ->assertDontSee('data-cachet-metric', escape: false);

    Metric::factory()->create([
        'visible' => ResourceVisibilityEnum::guest,
        'display_chart' => true,
        'show_when_empty' => true,
    ]);
    Cache::forget('cachet::metrics.guests');

    $this->get(route('cachet.status-page'))
        ->assertOk()
        ->assertSee('data-cachet-metric', escape: false)
        ->assertSee('x-on:keydown.arrow-right.prevent', escape: false)
        ->assertSee('x-on:keydown.arrow-left.prevent', escape: false)
        ->assertSee('x-on:keydown.home.prevent', escape: false)
        ->assertSee('x-on:keydown.end.prevent', escape: false);
});

it('can hide component group statuses', function () {
    $settings = app(AppSettings::class);
    $settings->show_component_group_status = false;
    $settings->save();

    $group = ComponentGroup::factory()->create(['name' => 'Core services']);
    $firstComponent = Component::factory()->create([
        'component_group_id' => $group->id,
        'status' => ComponentStatusEnum::major_outage,
    ]);
    $secondComponent = Component::factory()->create(['component_group_id' => $group->id]);
    $incident = Incident::factory()->create(['status' => IncidentStatusEnum::investigating]);
    $incident->components()->attach([$firstComponent->id, $secondComponent->id]);

    $page = $this->get(route('cachet.status-page'))
        ->assertOk()
        ->getContent();

    expect($page)
        ->toContain('Core services')
        ->toContain('1 Incident')
        ->not->toMatch('/Core services\s*<\\/h2>\s*<span[^>]*>\s*Major outage\s*<\\/span>/');
});

it('can display component tags', function () {
    $component = Component::factory()->create(['description' => null]);
    $component->syncTags(['API']);

    $settings = app(AppSettings::class);
    $settings->show_component_tags = true;
    $settings->save();

    $this->get(route('cachet.status-page'))->assertSee('API');
});

it('renders the status page in the configured locale', function () {
    $settings = app(AppSettings::class);
    $settings->locale = 'de';
    $settings->save();

    $this->get(route('cachet.status-page'))->assertOk();

    expect(app()->getLocale())->toBe('de');
});

it('does not error when the from query parameter is malformed', function () {
    $this->get(route('cachet.status-page', ['from' => '2024-04-15/']))
        ->assertOk();
});

it('does not error when the from query parameter is not a date', function () {
    $this->get(route('cachet.status-page', ['from' => 'not-a-date']))
        ->assertOk();
});

it('uses indexable timestamp ranges for the recent timeline', function () {
    $settings = app(AppSettings::class);
    $settings->recent_incidents_only = true;
    $settings->save();

    Incident::factory()->create(['occurred_at' => now()]);
    Schedule::factory()->completed()->create(['completed_at' => now()->subHour()]);

    $timelineQueries = [];

    DB::listen(function ($query) use (&$timelineQueries): void {
        if (str_contains($query->sql, 'occurred_at') || str_contains($query->sql, 'completed_at')) {
            $timelineQueries[] = $query->sql;
        }
    });

    $this->get(route('cachet.status-page'))->assertOk();

    expect($timelineQueries)->not->toBeEmpty()
        ->and(implode(' ', $timelineQueries))->not->toMatch('/(?:date|strftime)\s*\(/i');
});

it('shows upcoming and in progress maintenance in the maintenance block', function () {
    $upcoming = Schedule::factory()->inTheFuture()->create(['name' => 'Upcoming maintenance']);
    $inProgress = Schedule::factory()->inProgress()->create(['name' => 'In progress maintenance']);
    $completed = Schedule::factory()->inThePast()->create(['name' => 'Completed maintenance']);

    $response = $this->get(route('cachet.status-page'))->assertOk();

    $maintenanceBlock = $response->viewData('schedules');

    expect($maintenanceBlock->pluck('id'))
        ->toContain($upcoming->id, $inProgress->id)
        ->not->toContain($completed->id);
});

it('shows completed maintenance in the timeline instead of the maintenance block', function () {
    $completed = Schedule::factory()->completed()->create(['name' => 'Completed maintenance']);

    $response = $this->get(route('cachet.status-page'))->assertOk();

    expect($response->viewData('schedules')->pluck('id'))->not->toContain($completed->id);

    $response->assertSee('Completed maintenance');
});

it('shows stickied incidents at the top of the timeline', function () {
    Incident::factory()->create([
        'name' => 'Pinned incident',
        'stickied' => true,
        'occurred_at' => now()->subMonths(2),
    ]);
    Incident::factory()->create([
        'name' => 'Recent incident',
        'occurred_at' => now(),
    ]);

    $response = $this->get(route('cachet.status-page'))
        ->assertOk()
        ->assertSeeInOrder(['Pinned incident', 'Recent incident']);

    expect(substr_count($response->getContent(), 'Pinned incident'))->toBe(1);
});

it('does not render a dynamic favicon when the setting is disabled', function () {
    Component::factory()->create(['status' => ComponentStatusEnum::major_outage]);

    $this->get(route('cachet.status-page'))
        ->assertOk()
        ->assertDontSee('favicon-major-outage.svg');
});

it('renders the favicon for the current system status when dynamic favicons are enabled', function (array $componentStatuses, string $favicon) {
    $settings = app(AppSettings::class);
    $settings->dynamic_favicon = true;
    $settings->save();

    foreach ($componentStatuses as $componentStatus) {
        Component::factory()->create(['status' => $componentStatus]);
    }

    $this->get(route('cachet.status-page'))
        ->assertOk()
        ->assertSee($favicon);
})->with([
    'partial outage' => [[ComponentStatusEnum::operational, ComponentStatusEnum::partial_outage], 'favicon-partial-outage.svg'],
    'major outage' => [[ComponentStatusEnum::major_outage], 'favicon-major-outage.svg'],
    'under maintenance' => [[ComponentStatusEnum::under_maintenance], 'favicon-under-maintenance.svg'],
]);

it('falls back to the default favicon when operational and dynamic favicons are enabled', function () {
    $settings = app(AppSettings::class);
    $settings->dynamic_favicon = true;
    $settings->save();

    Component::factory()->create(['status' => ComponentStatusEnum::operational]);

    $this->get(route('cachet.status-page'))
        ->assertOk()
        ->assertSee('favicon.ico')
        ->assertDontSee('image/svg+xml');
});

it('does not link a component to a javascript url', function () {
    Component::factory()->create([
        'name' => 'Scriptable API',
        'link' => 'javascript:alert(1)',
    ]);

    $this->get(route('cachet.status-page'))
        ->assertOk()
        ->assertSee('Scriptable API')
        ->assertDontSee('javascript:alert(1)', escape: false);
});

it('links a component to an http url', function () {
    Component::factory()->create([
        'name' => 'Linked API',
        'link' => 'https://status.example.com/api',
    ]);

    $this->get(route('cachet.status-page'))
        ->assertOk()
        ->assertSee('href="https://status.example.com/api"', escape: false);
});

it('does not render raw html in component descriptions', function () {
    Component::factory()->create([
        'description' => 'The **primary** API <script>alert(1)</script>',
    ]);

    $this->get(route('cachet.status-page'))
        ->assertOk()
        ->assertSee('<strong>primary</strong>', escape: false)
        ->assertDontSee('<script>alert(1)</script>', escape: false);
});

it('renders timestamps with the configured display timezone and timezone name', function () {
    $occurredAt = now()->subDay()->startOfMinute();

    Incident::factory()->create([
        'name' => 'Timezone incident',
        'occurred_at' => $occurredAt,
    ]);

    $this->get(route('cachet.status-page'))
        ->assertOk()
        ->assertSee("timeZone: 'UTC'", escape: false)
        ->assertSee("timeZoneName: 'short'", escape: false)
        ->assertSee('datetime="'.$occurredAt->toW3cString().'"', escape: false);
});

it('omits the explicit timezone when the browser default sentinel is set', function () {
    $settings = app(AppSettings::class);
    $settings->timezone = '-';
    $settings->save();

    Incident::factory()->create([
        'name' => 'Timezone incident',
        'occurred_at' => now()->subDay()->startOfMinute(),
    ]);

    $this->get(route('cachet.status-page'))
        ->assertOk()
        ->assertSee("timeZoneName: 'short'", escape: false)
        ->assertDontSee("timeZone: '", escape: false);
});

it('shows the UTC instant in the timestamp tooltip', function () {
    $occurredAt = now()->subDay()->startOfMinute();

    Incident::factory()->create([
        'name' => 'Timezone incident',
        'occurred_at' => $occurredAt,
    ]);

    $this->get(route('cachet.status-page'))
        ->assertOk()
        ->assertSee($occurredAt->format('Y-m-d H:i').' UTC');
});
