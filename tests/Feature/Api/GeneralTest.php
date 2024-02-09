<?php

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use function Pest\Laravel\getJson;

it('can ping the API')
    ->get('/status/api/ping')
    ->assertOk()
    ->assertJson(['data' => 'Pong!']);

it('can get the API version')
    ->get('/status/api/version')
    ->assertOk()
    ->assertJsonStructure(['data' => ['version']]);

it('can get the status', function () {
    Component::factory(2)->create([
        'status' => ComponentStatusEnum::partial_outage,
    ]);

    getJson('/status/api/status')
        ->assertOk()
        ->assertJson([
            'data' => [
                'attributes' => [
                    'name' => 'Cachet',
                    'label' => __('Some systems are experiencing issues.'),
                    'status' => 'partial_outage',
                ],
            ],
        ]);
});
