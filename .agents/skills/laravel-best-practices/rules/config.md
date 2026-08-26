# Configuration Best Practices

## `env()` Only in Config Files

Direct `env()` calls may return `null` when config is cached.

Incorrect:
```php
$key = env('API_KEY');
```

Correct:
```php
// config/services.php
'key' => env('API_KEY'),

// Application code
$key = config('services.key');
```

## Leave Host Secrets to the Consumer

Never store production secrets in plain `.env` files in version control.

Incorrect:
```bash
# .env committed to repo or shared in Slack
STRIPE_SECRET=sk_live_abc123
AWS_SECRET_ACCESS_KEY=wJalrXUtnFEMI
```

Core supplies namespaced configuration defaults and must not encrypt, decrypt, publish, or otherwise manage a consumer application's environment file. Consumers should inject secrets through their deployment platform and reference them from published configuration only when necessary.

## Use `App::environment()` for Environment Checks

Incorrect:
```php
if (env('APP_ENV') === 'production') {
```

Correct:
```php
if (app()->isProduction()) {
// or
if (App::environment('production')) {
```

## Use Constants and Language Files

Use class constants instead of hardcoded magic strings for model states, types, and statuses.

```php
// Incorrect
return $this->type === 'normal';

// Correct
return $this->type === self::TYPE_NORMAL;
```

If the application already uses language files for localization, use `__()` for user-facing strings too. Do not introduce language files purely for English-only apps — simple string literals are fine there.

```php
// Only when lang files already exist in the project
return back()->with('message', __('app.article_added'));
```
