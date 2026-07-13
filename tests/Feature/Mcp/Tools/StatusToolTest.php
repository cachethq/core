<?php

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Mcp\CachetServer;
use Cachet\Mcp\Tools\Status\GetStatus;
use Cachet\Models\Component;

it('reports an operational system', function () {
    Component::factory()->create(['status' => ComponentStatusEnum::operational]);

    CachetServer::tool(GetStatus::class)
        ->assertOk()
        ->assertSee('operational');
});

it('reports a major outage', function () {
    Component::factory(2)->create(['status' => ComponentStatusEnum::major_outage]);

    CachetServer::tool(GetStatus::class)
        ->assertOk()
        ->assertSee('major_outage');
});
