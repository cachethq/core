<?php

namespace Cachet\Database\Seeders;

use Cachet\Enums\ComponentGroupVisibilityEnum;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\IncidentTemplateEngineEnum;
use Cachet\Enums\MetricTypeEnum;
use Cachet\Enums\MetricViewEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Cachet\Models\Incident;
use Cachet\Models\IncidentTemplate;
use Cachet\Models\Metric;
use Cachet\Models\Schedule;
use Cachet\Settings\AppSettings;
use Cachet\Settings\CustomizationSettings;
use Cachet\Settings\ThemeSettings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->truncate();
        DB::table('incidents')->truncate();
        DB::table('incident_updates')->truncate();
        DB::table('components')->truncate();
        DB::table('component_groups')->truncate();
        DB::table('schedules')->truncate();
        DB::table('metrics')->truncate();
        DB::table('metric_points')->truncate();

        /** @var \Illuminate\Foundation\Auth\User $userModel */
        $userModel = config('cachet.user_model');

        $user = $userModel::create([
            'name' => 'Cachet Demo',
            'email' => 'test@test.com',
            'password' => bcrypt('test123'),
            'email_verified_at' => now(),
        ]);

        Schedule::create([
            'name' => 'Documentation Maintenance',
            'message' => 'We will be conducting maintenance on our documentation servers. Documentation may not be available during this time.',
            'scheduled_at' => now()->subHours(12)->subMinutes(45),
            'completed_at' => now()->subHours(12),
        ]);

        Schedule::create([
            'name' => 'Documentation Maintenance',
            'message' => 'We will be conducting maintenance on our documentation servers. You may experience degraded performance during this time.',
            'scheduled_at' => now()->addHours(24),
            'completed_at' => null,
        ]);

        $componentGroup = ComponentGroup::create([
            'name' => 'Cachet',
            'collapsed' => ComponentGroupVisibilityEnum::expanded,
            'visible' => ResourceVisibilityEnum::guest,
        ]);

        $componentGroup->components()->createMany([
            [
                'name' => 'Cachet Website',
                'description' => 'The Cachet Website.',
                'link' => 'https://cachethq.io',
                'status' => ComponentStatusEnum::operational,
            ], [
                'name' => 'Cachet Documentation',
                'description' => 'The Checkmango Documentation.',
                'link' => 'https://docs.cachethq.io',
                'status' => ComponentStatusEnum::operational,
            ], [
                'name' => 'Cachet Blog',
                'description' => 'The Cachet Blog.',
                'link' => 'https://blog.cachethq.io',
                'status' => ComponentStatusEnum::operational,
            ],
        ]);

        Component::create([
            'name' => 'Laravel Artisan Cheatsheet',
            'description' => 'The Laravel Artisan Cheatsheet.',
            'link' => 'https://artisan.page',
            'status' => ComponentStatusEnum::operational,
        ]);

        $metric = Metric::create([
            'name' => 'Cachet API Requests',
            'suffix' => 'req/s',
            'description' => 'The number of requests to the Cachet API.',
            'default_view' => MetricViewEnum::last_hour,
            'calc_type' => MetricTypeEnum::average,
            'display_chart' => true,
            'places' => 2,
            'default_value' => 0,
        ]);

        $metric->metricPoints()->createMany(Arr::map(range(1, 60), fn (int $i) => [
            'value' => random_int(1, 100),
            'created_at' => now()->subMinutes(random_int(0, $i * 60)),
        ]));

        tap(Incident::create([
            'name' => 'DNS Provider Outage',
            'message' => 'We\'re investigating an issue with our DNS provider causing the site to be offline.',
            'status' => IncidentStatusEnum::fixed,
            'visible' => ResourceVisibilityEnum::guest,
            'guid' => Str::uuid(),
            'user_id' => $user->id,
            'created_at' => $timestamp = now()->subDay(),
            'updated_at' => $timestamp,
            'occurred_at' => $timestamp,
        ]), function (Incident $incident) use ($user) {
            $incident->incidentUpdates()->create([
                'status' => IncidentStatusEnum::identified,
                'message' => 'We\'ve confirmed the issue is with our DNS provider. We\'re waiting on them to provide an ETA.',
                'user_id' => $user->id,
                'created_at' => $timestamp = $incident->created_at->addMinutes(30),
                'updated_at' => $timestamp,
            ]);

            $incident->incidentUpdates()->create([
                'status' => IncidentStatusEnum::fixed,
                'message' => <<<'EOF'
Our DNS provider has fixed the issue. We will continue to monitor the situation.

For more information, please you can read our latest [blog post](https://blog.cachethq.io).
EOF
                ,
                'user_id' => $user->id,
                'created_at' => $timestamp = $incident->created_at->addMinutes(45),
                'updated_at' => $timestamp,
            ]);
        });

        $incident = Incident::create([
            'name' => 'Documentation Performance Degradation',
            'message' => 'We\'re investigating an issue with our documentation causing the site to be slow.',
            'status' => IncidentStatusEnum::fixed,
            'visible' => ResourceVisibilityEnum::guest,
            'guid' => Str::uuid(),
            'created_at' => $timestamp = now()->subMinutes(30),
            'updated_at' => $timestamp,
            'occurred_at' => $timestamp,
        ]);

        $incident->incidentUpdates()->create([
            'status' => IncidentStatusEnum::identified,
            'message' => 'We\'ve identified the issue and are working on a fix.',
            'created_at' => $timestamp = $incident->created_at->addMinutes(15),
            'updated_at' => $timestamp,
        ]);

        $incident->incidentUpdates()->create([
            'status' => IncidentStatusEnum::fixed,
            'message' => 'The documentation is now back online. Happy reading!',
            'created_at' => $timestamp = $incident->created_at->addMinutes(25),
            'updated_at' => $timestamp,
        ]);

        IncidentTemplate::create([
            'name' => 'Third-Party Service Outage',
            'slug' => 'third-party-service-outage',
            'template' => 'We\'re investigating an issue with a third-party provider ({{ name }}) causing our services to be offline.',
            'engine' => IncidentTemplateEngineEnum::twig,
        ]);

        $appSettings = app(AppSettings::class);
        $appSettings->name = 'Cachet v3.x Demo';
        $appSettings->about = <<<'ABOUT'
Cachet is a **beautiful** and **powerful** open-source status page system.

To access the [dashboard](/dashboard), use the following credentials:
- `test@test.com`
- `test123`

Please [consider sponsoring](https://github.com/cachethq/cachet?sponsor=1) the continued development of Cachet.
ABOUT;
        $appSettings->show_support = true;
        $appSettings->timezone = 'UTC';
        $appSettings->show_timezone = false;
        $appSettings->only_disrupted_days = false;
        $appSettings->incident_days = 7;
        $appSettings->refresh_rate = null;
        $appSettings->dashboard_login_link = true;
        $appSettings->major_outage_threshold = 25;
        $appSettings->save();

        $customizationSettings = app(CustomizationSettings::class);
        $customizationSettings->header = <<<'HTML'
<script src="https://cdn.usefathom.com/script.js" data-site="NQKCLYJJ" defer></script>
HTML;
        $customizationSettings->footer = '';
        $customizationSettings->stylesheet = '';
        $customizationSettings->save();

        $themeSettings = app(ThemeSettings::class);
        $themeSettings->app_banner = '';
        $themeSettings->save();
    }
}
