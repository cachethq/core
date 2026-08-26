# Package Development

Cachet Core is a Laravel package, not the complete host application. Preserve the boundary between package-owned code and the consumer application.

## Package Layout

Map application-oriented examples to the existing package structure:

| Concern | Package path |
| --- | --- |
| PHP source | `src/` under the `Cachet\\` namespace |
| Controllers and middleware | `src/Http/` |
| Models | `src/Models/` |
| Filament resources, pages, and widgets | `src/Filament/` |
| MCP servers and tools | `src/Mcp/` |
| Configuration | `config/` |
| Routes | `routes/` or package service-provider registration |
| Migrations and factories | `database/` |
| Views, translations, CSS, and JavaScript | `resources/` |

Do not create an `app/` tree or put domain implementation in `workbench/`.

## Host Integration

- Register framework integration through `CachetCoreServiceProvider` or `CachetDashboardServiceProvider`.
- Load package routes, migrations, views, and translations through Laravel's package APIs.
- Never edit or assume control of the host application's bootstrap files, route files, exception handler, scheduler, or default configuration.
- Keep consumer overrides working through namespaced views, configuration, publishing tags, middleware groups, and documented extension points.

## Compatibility

- Support every Laravel version allowed by `composer.json`.
- Prefer APIs shared by those versions. Add a version-specific branch only when the behavior is required and covered by tests.
- Treat route names, configuration keys, migration history, publish tags, view namespaces, and public PHP methods as compatibility contracts.
- Package migrations must upgrade existing Cachet installations safely and must not assume ownership of unrelated application tables.

## Commands and Tests

Run Artisan with `vendor/bin/testbench`. Generator output may land in Testbench's application skeleton; use it only as a reference and place final code in the matching package directory.

Use Orchestra Testbench feature tests for Laravel integration. The `workbench/` directory is a host fixture and should change only when a realistic host application needs new setup.

## Assets

Edit source assets under `resources/`, run `npm run build`, and publish distributable output from `public/`. Testbench's mirrored public assets are disposable build output.
