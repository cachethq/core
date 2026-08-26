---
name: mcp-development
description: "Use this skill for Cachet's Laravel MCP server. Trigger when creating or editing MCP tools, resources, prompts, server registration, schemas, annotations, authentication, authorization, visibility, or MCP tests. Do not use for non-Laravel MCP projects or generic AI features without MCP."
license: MIT
metadata:
  author: laravel
---
# MCP Development

## Documentation First

**CRITICAL**: Always use `search-docs` BEFORE writing MCP code. The documentation is version-specific, comprehensive, and always up-to-date.

```bash
# Example searches
search-docs(['mcp tools', 'mcp resources', 'mcp validation'])
```

## Core Package Conventions

- Keep the server and primitives under `src/Mcp` in the `Cachet\\Mcp` namespace.
- Register the web server through `CachetCoreServiceProvider`; do not create `routes/ai.php` or modify a host application.
- Do not use `make:mcp-*` generators for final code. They target Testbench's host skeleton. Follow sibling tools and create the package file in `src/Mcp`.
- Keep server availability, authentication, rate limiting, token abilities, and record visibility aligned with `config/cachet.php`, the MCP middleware, and existing concerns.
- Every write tool must guard the matching Sanctum ability in both `handle()` and `shouldRegister()`.
- Use the Laravel MCP annotation matching the side effect, including `#[IsReadOnly]`, `#[IsIdempotent]`, and `#[IsDestructive]` where applicable.
- Put feature coverage under `tests/Feature/Mcp` and architecture coverage under `tests/Architecture/McpTest.php`.

## Quick Reference

### Package Layout

Inspect the closest tool and its feature test before adding a primitive. Reuse `GuardsMcpAbilities`, `PresentsResources`, visibility scopes, pagination, and domain actions instead of duplicating those mechanics.

### Basic Tool Implementation

```php
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class MyTool extends Tool
{
    protected string $description = 'Tool description for LLM';

    public function schema(JsonSchema $schema): array
    {
        return [
            'param' => $schema->string()->required(),
        ];
    }

    public function handle(Request $request): Response
    {
        return Response::text($request->get('param'));
    }
}
```

### Basic Resource Implementation

```php
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Resource;

class MyResource extends Resource
{
    protected string $description = 'Resource description';
    protected string $uri = 'file://path/to/resource';
    protected string $mimeType = 'text/markdown';

    public function handle(): Response
    {
        return Response::text($content);
    }
}
```

### Response Methods

```php
Response::text('Text content');
Response::error('Error message');
Response::structured(['key' => 'value']);
```

## Testing MCP Primitives

Test tools, resources, and prompts directly on their server:

```php
// Test a tool
$response = CachetServer::tool(MyTool::class, ['param' => 'value']);
$response->assertOk()->assertSee('Expected text');

// Test as an authenticated user with the matching token ability
Sanctum::actingAs($user, ['resource.manage']);
$response = CachetServer::tool(MyTool::class, [...]);

// Available assertions
$response->assertOk();
$response->assertSee('text');
$response->assertHasErrors();
$response->assertHasNoErrors();
$response->assertName('tool-name');
$response->assertSentNotification('event/type', ['data' => 'value']);
```

For write tools, also assert the database change, side effects, missing ability, and whether the tool is hidden from callers without that ability. Test status-page visibility whenever the tool reads a scoped record.

### MCP Inspector

Test interactively using the inspector:

```bash
vendor/bin/testbench route:list --name=cachet.mcp
vendor/bin/testbench mcp:inspector mcp
```

The inspector is interactive and may install frontend tooling. Run it only when interactive debugging is needed.

## Available Features

The following features exist—**use `search-docs` for implementation details**:

- **Tools**: `schema()`, validation, annotations (`#[IsReadOnly]`, `#[IsDestructive]`, etc.)
- **Resources**: URI templates (`HasUriTemplate`), Dynamic resources
- **Prompts**: Arguments, multi-message responses
- **All primitives**: Dependency injection, `shouldRegister()`, validation
- **Responses**: Text, error, structured, streaming, metadata
- **Server registration**: Web routes, local routes, OAuth

## Critical Imports

```php
use Laravel\Mcp\Request;           // NOT Laravel\Mcp\Server\Request
use Laravel\Mcp\Response;          // NOT Laravel\Mcp\Server\Response
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Resource;
use Laravel\Mcp\Server\Prompt;
use Illuminate\Contracts\JsonSchema\JsonSchema;
```

## Common Pitfalls

- **Not using `search-docs` before implementation**
- Wrong imports: `Laravel\Mcp\Server\Request` (wrong) vs `Laravel\Mcp\Request` (correct)
- Forgetting `schema()` method for tools with parameters
- Missing required properties: `$description`, `$uri`, `$mimeType`
- Wrong response pattern: `new Response()` instead of `Response::text()`
- Generating final package code into Testbench's application skeleton
- Registering a second server in `routes/ai.php` instead of extending `CachetServer`
- Exposing a write tool without the matching Sanctum ability and registration guard
- Returning records without Cachet's visibility scope
- Running `mcp:start` command locally (hangs waiting for stdin)
