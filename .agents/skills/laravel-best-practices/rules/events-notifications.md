# Events & Notifications Best Practices

## Register Package Listeners Explicitly

Do not assume the host application's event discovery scans package source. Register Cachet listeners through a package service provider and preserve compatibility with cached events.

## Leave Deployment Caching to the Consumer

Core must work when a consumer caches events and configuration, but the package does not own the consumer's deployment commands. Never run `optimize` or `event:cache` as part of package installation.

## Use `ShouldDispatchAfterCommit` Inside Transactions

Without it, a queued listener may process before the DB transaction commits, reading data that doesn't exist yet.

```php
class OrderShipped implements ShouldDispatchAfterCommit {}
```

## Always Queue Notifications

Notifications often hit external APIs (email, SMS, Slack). Without `ShouldQueue`, they block the HTTP response.

```php
class InvoicePaid extends Notification implements ShouldQueue
{
    use Queueable;
}
```

## Use `afterCommit()` on Notifications in Transactions

Same race condition as events — call `afterCommit()` to delay dispatch until the transaction commits.

```php
$user->notify((new InvoicePaid($invoice))->afterCommit());
```

## Route Notification Channels to Dedicated Queues

Mail and database notifications have different priorities. Use `viaQueues()` to route them to separate queues.

## Use On-Demand Notifications for Non-User Recipients

Avoid creating dummy models to send notifications to arbitrary addresses.

```php
Notification::route('mail', 'admin@example.com')->notify(new SystemAlert());
```

## Implement `HasLocalePreference` on Notifiable Models

Laravel automatically uses the user's preferred locale for all notifications and mailables — no per-call `locale()` needed.
