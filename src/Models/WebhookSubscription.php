<?php

namespace Cachet\Models;

use Cachet\Cachet;
use Cachet\Database\Factories\WebhookSubscriptionFactory;
use Cachet\Enums\WebhookEventEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\WebhookServer\WebhookCall;

class WebhookSubscription extends Model
{
    /** @use HasFactory<WebhookSubscriptionFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'url',
        'secret',
        'description',
        'send_all_events',
        'selected_events',
    ];

    protected $hidden = [
        'secret',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'selected_events' => AsEnumCollection::class.':'.WebhookEventEnum::class,
    ];

    /**
     * Scope to subscriptions that are enabled for the given event.
     */
    public function scopeWhereEvent(Builder $builder, WebhookEventEnum $event)
    {
        return $builder->where(function ($query) use ($event) {
            $query->where('send_all_events', true)
                ->orWhereJsonContains('selected_events', $event->value);
        });
    }

    /**
     * Get the attempts for this subscription.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(WebhookAttempt::class, 'subscription_id')->latest();
    }

    /**
     * Make a webhook call to this subscriber for the given event and payload.
     */
    public function makeCall(WebhookEventEnum $event, array $payload): WebhookCall
    {
        return WebhookCall::create()
            ->url($this->url)
            ->withHeaders([
                'User-Agent' => Cachet::WEBHOOK_USER_AGENT,
            ])
            ->payload([
                'event' => $event->value,
                'body' => $payload,
            ])
            ->meta([
                'subscription_id' => $this->getKey(),
                'event' => $event->value,
            ])
            ->onConnection(config('cachet.webhooks.queue.connection'))
            ->onQueue(config('cachet.webhooks.queue.name'))
            ->useSecret($this->secret);
    }

    /**
     * Formats the success rate as a percentage.
     */
    public function getSuccessRate24hAttribute($value)
    {
        return number_format(($value ?? 0) * 100, 2).'%';
    }

    /**
     * Recalculate the success rate for the last 24 hours based on the attempts.
     */
    public function recalculateSuccessRate(): self
    {
        $attempts24hQuery = $this->attempts()->where('created_at', '>=', now()->subDay());
        $totalAttempts24h = $attempts24hQuery->count();
        $successfulAttempts24h = $attempts24hQuery->whereSuccessful()->count();

        $this->success_rate_24h = $totalAttempts24h > 0 ? $successfulAttempts24h / $totalAttempts24h : 0;

        return $this;
    }
}
