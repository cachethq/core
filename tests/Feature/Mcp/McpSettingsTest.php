<?php

use Cachet\Settings\AppSettings;
use Laravel\Sanctum\Sanctum;
use Workbench\App\User;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

function mcpRequest(string $method, array $params = []): array
{
    return [
        'jsonrpc' => '2.0',
        'id' => 1,
        'method' => $method,
        'params' => $params,
    ];
}

function mcpInitializeRequest(): array
{
    return mcpRequest('initialize', [
        'protocolVersion' => '2025-03-26',
        'capabilities' => [],
        'clientInfo' => ['name' => 'pest', 'version' => '1.0.0'],
    ]);
}

function enableMcp(bool $protected = true): void
{
    $settings = app(AppSettings::class);
    $settings->mcp_enabled = true;
    $settings->mcp_protected = $protected;
    $settings->save();
}

it('responds not found by default', function () {
    postJson('/status/mcp', mcpInitializeRequest())
        ->assertNotFound();
});

it('responds not found when disabled, even for authenticated users', function () {
    Sanctum::actingAs(User::factory()->create(), ['*']);

    postJson('/status/mcp', mcpInitializeRequest())
        ->assertNotFound();
});

it('requires authentication when the mcp server is protected', function () {
    enableMcp(protected: true);

    postJson('/status/mcp', mcpInitializeRequest())
        ->assertUnauthorized();
});

it('allows authenticated clients to connect when the mcp server is protected', function () {
    enableMcp(protected: true);

    Sanctum::actingAs(User::factory()->create(), ['*']);

    postJson('/status/mcp', mcpInitializeRequest())
        ->assertOk()
        ->assertJsonPath('result.serverInfo.name', 'Cachet');
});

it('allows guests to connect when the mcp server is not protected', function () {
    enableMcp(protected: false);

    postJson('/status/mcp', mcpInitializeRequest())
        ->assertOk()
        ->assertJsonPath('result.serverInfo.name', 'Cachet');
});

it('responds method not allowed to get requests when enabled', function () {
    enableMcp(protected: false);

    getJson('/status/mcp')
        ->assertStatus(405);
});

it('responds not found to get requests when disabled', function () {
    getJson('/status/mcp')
        ->assertNotFound();
});

it('only exposes read tools to guests', function () {
    enableMcp(protected: false);

    $tools = collect(postJson('/status/mcp', mcpRequest('tools/list'))
        ->assertOk()
        ->json('result.tools'))
        ->pluck('name');

    expect($tools)
        ->toContain('get_status')
        ->toContain('list_incidents')
        ->toContain('list_components')
        ->not->toContain('create_incident')
        ->not->toContain('delete_component')
        ->not->toContain('list_subscribers');
});

it('exposes write tools matching the token abilities', function () {
    enableMcp(protected: false);

    Sanctum::actingAs(User::factory()->create(), ['incidents.manage']);

    $tools = collect(postJson('/status/mcp', mcpRequest('tools/list'))
        ->assertOk()
        ->json('result.tools'))
        ->pluck('name');

    expect($tools)
        ->toContain('create_incident')
        ->toContain('update_incident')
        ->not->toContain('delete_incident')
        ->not->toContain('create_component');
});

it('exposes every tool to tokens with all abilities', function () {
    enableMcp(protected: false);

    Sanctum::actingAs(User::factory()->create(), ['*']);

    $tools = collect(postJson('/status/mcp', mcpRequest('tools/list', ['per_page' => 50]))
        ->assertOk()
        ->json('result.tools'))
        ->pluck('name');

    expect($tools)->toHaveCount(41);
});

it('does not allow guests to call write tools', function () {
    enableMcp(protected: false);

    postJson('/status/mcp', mcpRequest('tools/call', [
        'name' => 'create_incident',
        'arguments' => ['name' => 'Incident', 'status' => 1, 'message' => 'Test'],
    ]))
        ->assertOk()
        ->assertJsonPath('error.message', 'Tool [create_incident] not found.');
});

it('allows guests to call read tools', function () {
    enableMcp(protected: false);

    postJson('/status/mcp', mcpRequest('tools/call', [
        'name' => 'list_components',
        'arguments' => [],
    ]))
        ->assertOk()
        ->assertJsonPath('result.isError', false);
});
