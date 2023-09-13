<?php

it('can ping the API')
    ->get('/status/api/ping')
    ->assertOk()
    ->assertJson(['data' => 'Pong!']);

it('can get the API version')
    ->get('/status/api/version')
    ->assertOk()
    ->assertJsonStructure(['data' => ['version']]);
