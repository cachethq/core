# Cachet Core - Agent Instructions

This document provides comprehensive guidelines for AI agents and developers working on the Cachet Core repository.
Adhere to these rules to maintain code quality, consistency, and stability.

## 1. Project Context

Cachet Core is a Laravel package that provides the core functionality for Cachet (Status Page system).
It leverages the Laravel ecosystem extensively, including Filament for the admin panel.

- **Framework:** Laravel (Package development via Orchestra Testbench)
- **Language:** PHP 8.2+
- **Admin UI:** Filament v4
- **Frontend:** Blade, Alpine.js, Tailwind CSS
- **Testing:** Pest PHP
- **Static Analysis:** Larastan (Level 5), Pint

## 2. Development Workflow

### Dependency Management

- **PHP:** `composer install`
- **JS:** `npm install`
- **Do not** add new dependencies without explicit approval.

### Build Commands

- **Frontend Build:** `npm run build` (Required for CSS/JS changes)
- **Frontend Dev:** `npm run dev` (Watch mode)
- **Package Discovery:** `composer prepare` (Runs `package:discover`)

### Local Development (Workbench)

- The project uses `orchestra/testbench` with a `workbench` directory.
- **Serve App:** `composer start` or `composer dev`
- **Clear Cache:** `composer clear`

## 3. Testing & Verification

**Always** run tests before and after changes.

### Running Tests (Pest)

- **Run All Units:** `composer test:unit` (or `composer test`)
- **Run Single Test (Filter):**
    ```bash
    vendor/bin/pest --filter 'UserCanLoginTest'
    ```
- **Run Single File:**
    ```bash
    vendor/bin/pest tests/Feature/AuthenticationTest.php
    ```
- **Parallel Testing:** `composer test:unit` is configured for parallel execution.

### Linting & Static Analysis

- **Check PHP Style:** `composer test:lint` (Runs Pint)
- **Fix PHP Style:** `vendor/bin/pint`
- **Static Analysis:** `vendor/bin/phpstan analyse` (Configured in `phpstan.neon.dist`)
- **JS/Blade Formatting:** `npm run fix:prettier`

**Note:** Do not create ad-hoc verification scripts. Use existing tests or write new Pest tests.

## 4. Code Style & Conventions

### PHP (Laravel/Pint)

- **Strict Types:** Use `declare(strict_types=1);` where appropriate, though check sibling files for consistency.
- **Naming:**
    - Use **descriptive** names.
    - Boolean methods: `isRegistered()`, `hasPermission()`, not `registered()` or `check()`.
    - Variables: `$user`, `$statusPage`, not `$u`, `$sp`.
- **Controllers:** Keep thin. Push logic to Actions, Services, or Models.
- **Models:** Use `Fillable` or `Guarded` explicitly.
- **Imports:** Sort alphabetically. Remove unused imports.

### Filament (Admin)

- Follow Filament v4 conventions for Resources, Pages, and Widgets.
- Use Filament's form components and table columns.

### Frontend (Alpine/Tailwind)

- **Blade:** Use components (`<x-cachet::...>`) where available.
- **Alpine.js:** Use `x-data` for component state. Avoid inline scripts where possible.
- **Tailwind:** Use utility classes. Avoid custom CSS unless necessary.

## 5. Directory Structure & Architecture

Stick to the existing structure. Do not invent new top-level directories.

- `src/`: Core package code (Models, Http, ServiceProviders).
- `tests/`: Pest tests (Feature, Unit).
- `resources/views/`: Blade templates.
- `database/`: Migrations, Factories, Seeders.
- `workbench/`: Testbench application skeleton.

## 6. Specific Rules (from CLAUDE.md)

- **Laravel Boost Guidelines:**
    - This project uses "Laravel Boost" rules.
    - Focus on existing code conventions.
    - **No Verification Scripts:** Trust unit/feature tests.
    - **Conciseness:** Be concise in explanations.
    - **Documentation:** Do not create `.md` files unless requested.

- **Debugging:**
    - Use `vendor/bin/testbench tinker` for interactive debugging.
    - Use `browser-logs` tool if available for frontend issues.

## 7. Example: Adding a New Feature

1. **Plan:** Identify necessary Models, Controllers, and Filament resources.
2. **Test:** Create a Pest test in `tests/Feature`.
    ```php
    it('can create a status', function () {
        $user = User::factory()->create();
        // ...
    });
    ```
3. **Implement:** Write code in `src/`.
4. **Verify:** Run `vendor/bin/pint` and `composer test:unit`.

## 8. Common Issues & Troubleshooting

- **"Class not found":** Run `composer dump-autoload`.
- **Frontend not updating:** Run `npm run build`.
- **Database issues:** Check `workbench/database` or run migrations in the test environment.

## 9. Error Handling

- Use Laravel's exception handling.
- Throw specific exceptions (e.g., `ModelNotFoundException`) rather than generic ones.
- Log errors using `Log::error()` with context.

## 10. Type Hinting & Docblocks

- Type hint all method arguments and return types.
- Use Docblocks (`/** ... */`) for:
    - Complex logic explanations.
    - `phpstan` type definitions (e.g., `/** @var array<string, int> $items */`).
    - Deprecation notices.

---

_Generated for AI Agents interacting with Cachet Core._

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.3.30

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan

- Use the `list-artisan-commands` tool when you need to call an Artisan command to double-check the available parameters.

## URLs

- Whenever you share a project URL with the user, you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain/IP, and port.

## Tinker / Debugging

- Because Cachet is a Laravel package, developed with Testbench, you should run Tinker with `vendor/bin/testbench tinker`.
- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.
- Use the `database-schema` tool to inspect table structure before writing migrations or models.

## Reading Browser Logs With the `browser-logs` Tool

- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)

- Boost comes with a powerful `search-docs` tool you should use before trying other approaches when working with Laravel or Laravel ecosystem packages. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic-based queries at once. For example: `['rate limiting', 'routing rate limiting', 'routing']`. The most relevant results will be returned first.
- Do not add package names to queries; package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'.
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit".
3. Quoted Phrases (Exact Position) - query="infinite scroll" - words must be adjacent and in that order.
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit".
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms.

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.

## Constructors

- Use PHP 8 constructor property promotion in `__construct()`.
    - `public function __construct(public GitHub $github) { }`
- Do not allow empty `__construct()` methods with zero parameters unless the constructor is private.

## Type Declarations

- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<!-- Explicit Return Types and Method Params -->
```php
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
```

## Enums

- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

## Comments

- Prefer PHPDoc blocks over inline comments. Never use comments within the code itself unless the logic is exceptionally complex.

## PHPDoc Blocks

- Add useful array shape type definitions when appropriate.
</laravel-boost-guidelines>
