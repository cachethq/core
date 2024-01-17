<?php

namespace Workbench\Database\Seeders;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\MetricTypeEnum;
use Cachet\Enums\MetricViewEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\ComponentGroup;
use Cachet\Models\Incident;
use Cachet\Models\Metric;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Workbench\App\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Cachet Demo',
            'email' => 'test@test.com',
            'password' => bcrypt('test123'),
            'email_verified_at' => now(),
            'active' => true,
        ]);

        $componentGroup = ComponentGroup::create([
            'name' => 'Checkmango',
            'visible' => ResourceVisibilityEnum::guest,
        ]);

        $componentGroup->components()->createMany([
            [
                'name' => 'Dashboard',
                'description' => 'The Checkmango Dashboard.',
                'link' => 'https://checkmango.com',
                'status' => ComponentStatusEnum::operational,
            ], [
                'name' => 'API',
                'description' => 'The Checkmango API.',
                'link' => 'https://developers.checkmango.com',
                'status' => ComponentStatusEnum::operational,
            ], [
                'name' => 'Documentation',
                'description' => 'The Checkmango Documentation.',
                'link' => 'https://docs.checkmango.com',
                'status' => ComponentStatusEnum::performance_issues,
            ],
        ]);

        $metric = Metric::create([
            'name' => 'Checkmango Requests',
            'suffix' => 'req/s',
            'description' => 'The number of requests to the Checkmango API.',
            'default_view' => MetricViewEnum::last_hour,
            'calc_type' => MetricTypeEnum::average,
            'display_chart' => true,
            'places' => 2,
            'default_value' => 0,
        ]);

        $metric->metricPoints()->createMany(Arr::map(range(1, 60), fn ($i) => [
            'value' => random_int(1, 100),
            'created_at' => now()->subMinutes($i),
        ]));

        $incident = Incident::create([
            'name' => 'Documentation Performance Degradation',
            'message' => 'We\'re investigating an issue with our documentation causing the site to be slow.',
            'status' => IncidentStatusEnum::investigating,
            'visible' => ResourceVisibilityEnum::guest,
            'guid' => Str::uuid(),
            'created_at' => $timestamp = now()->subMinutes(30),
            'updated_at' => $timestamp,
        ]);

        $incident->incidentUpdates()->create([
            'status' => IncidentStatusEnum::identified,
            'message' => 'We\'ve identified the issue and are working on a fix.',
        ]);
    }
}
