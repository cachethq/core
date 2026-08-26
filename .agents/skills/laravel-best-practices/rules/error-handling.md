# Error Handling Best Practices

## Exception Reporting and Rendering

Keep package-owned exception behavior on the exception class so Core does not need to modify a consumer application's exception configuration.

**Co-location on the exception class** — keeps behavior alongside the exception definition, easier to find:

```php
class InvalidOrderException extends Exception
{
    public function report(): void { /* custom reporting */ }

    public function render(Request $request): Response
    {
        return response()->view('errors.invalid-order', status: 422);
    }
}
```

Do not edit or require changes to the host application's `bootstrap/app.php` for package behavior.

## Use `ShouldntReport` for Exceptions That Should Never Log

More discoverable than listing classes in `dontReport()`.

```php
class PodcastProcessingException extends Exception implements ShouldntReport {}
```

## Force JSON Error Rendering for API Routes

Core cannot configure the host application's global exception renderer. Package API and MCP endpoints should return explicit responses where needed and tests should send the correct JSON accept headers.

## Add Context to Exception Classes

Attach structured data to exceptions at the source via a `context()` method — Laravel includes it automatically in the log entry.

```php
class InvalidOrderException extends Exception
{
    public function context(): array
    {
        return ['order_id' => $this->orderId];
    }
}
```
