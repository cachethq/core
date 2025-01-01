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
use Cachet\Models\Update;
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
        /** @var \Illuminate\Foundation\Auth\User $userModel */
        $userModel = config('cachet.user_model');

        $user = $userModel::query()->firstOrCreate([
            'name' => 'Cachet Demo',
            'email' => 'test@test.com',
            'password' => bcrypt('test123'),
            'email_verified_at' => now(),
        ]);

        Schedule::query()->firstOrCreate([
            'name' => 'Documentation Maintenance',
            'message' => 'We will be conducting maintenance on our documentation servers. Documentation may not be available during this time.',
            'scheduled_at' => now()->subHours(12)->subMinutes(45),
            'completed_at' => now()->subHours(12),
        ]);

        tap(Schedule::query()->firstOrCreate([
            'name' => 'Documentation Maintenance',
            'message' => 'We will be conducting maintenance on our documentation servers. You may experience degraded performance during this time.',
            'scheduled_at' => now()->addHours(24),
            'completed_at' => null,
        ]), function (Schedule $schedule) use ($user) {
            $update = new Update([
                'message' => <<<'EOF'
This scheduled maintenance period has been pushed back by one hour.
EOF
                ,
                'user_id' => $user->id,
                'created_at' => $timestamp = $schedule->created_at->addMinutes(45),
                'updated_at' => $timestamp,
            ]);

            $schedule->updates()->save($update);
        });

        $componentGroup = ComponentGroup::query()->firstOrCreate([
            'name' => 'Cachet',
            'collapsed' => ComponentGroupVisibilityEnum::expanded,
            'visible' => ResourceVisibilityEnum::guest,
        ]);

        /** @phpstan-ignore-next-line argument.type Larastan bug */
        $componentGroup->components()->createMany([
            [
                'name' => 'Cachet Website',
                'description' => 'The Cachet website.',
                'link' => 'https://cachethq.io',
                'status' => ComponentStatusEnum::operational,
            ],
            [
                'name' => 'Cachet Documentation',
                'description' => 'The Cachet docs, powered by Mintlify.',
                'link' => 'https://docs.cachethq.io',
                'status' => ComponentStatusEnum::operational,
            ],
            [
                'name' => 'Cachet Blog',
                'description' => 'Learn more about Cachet.',
                'link' => 'https://blog.cachethq.io',
                'status' => ComponentStatusEnum::operational,
            ],
        ]);

        Component::query()->firstOrCreate([
            'name' => 'Laravel Artisan Cheatsheet',
            'description' => 'The Laravel Artisan Cheatsheet.',
            'link' => 'https://artisan.page',
            'status' => ComponentStatusEnum::operational,
        ]);

        $metric = Metric::query()->firstOrCreate([
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

        tap(Incident::query()->firstOrCreate([
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
            $update = new Update([
                'status' => IncidentStatusEnum::identified,
                'message' => 'We\'ve confirmed the issue is with our DNS provider. We\'re waiting on them to provide an ETA.',
                'user_id' => $user->id,
                'created_at' => $timestamp = $incident->created_at->addMinutes(30),
                'updated_at' => $timestamp,
            ]);

            $incident->updates()->save($update);

            $update = new Update([
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

            $incident->updates()->save($update);
        });

        $incident = Incident::query()->firstOrCreate([
            'name' => 'Documentation Performance Degradation',
            'message' => 'We\'re investigating an issue with our documentation causing the site to be slow.',
            'status' => IncidentStatusEnum::fixed,
            'visible' => ResourceVisibilityEnum::guest,
            'guid' => Str::uuid(),
            'created_at' => $timestamp = now()->subMinutes(30),
            'updated_at' => $timestamp,
            'occurred_at' => $timestamp,
        ]);

        $update = new Update([
            'status' => IncidentStatusEnum::identified,
            'message' => 'We\'ve identified the issue and are working on a fix.',
            'created_at' => $timestamp = $incident->created_at->addMinutes(15),
            'updated_at' => $timestamp,
        ]);

        $incident->updates()->create([
            'status' => IncidentStatusEnum::fixed,
            'message' => 'The documentation is now back online. Happy reading!',
            'created_at' => $timestamp = $incident->created_at->addMinutes(25),
            'updated_at' => $timestamp,
        ]);

        $incident->updates()->save($update);

        IncidentTemplate::query()->firstOrCreate([
            'name' => 'Third-Party Service Outage',
            'slug' => 'third-party-service-outage',
            'template' => 'We\'re investigating an issue with a third-party provider ({{ name }}) causing our services to be offline.',
            'engine' => IncidentTemplateEngineEnum::twig,
        ]);

        $customizationSettings = app(CustomizationSettings::class);
        $customizationSettings->header = <<<'HTML'
<script src="https://cdn.usefathom.com/script.js" data-site="NQKCLYJJ" defer></script>
HTML;
        $customizationSettings->footer = '';
        $customizationSettings->stylesheet = '';
        $customizationSettings->save();

        $themeSettings = app(ThemeSettings::class);
        $themeSettings->app_banner = '';
        $themeSettings->accent = 'cachet';
        $themeSettings->accent_content = 'zinc';
        $themeSettings->accent_pairing = true;
        $themeSettings->save();
    }
}
