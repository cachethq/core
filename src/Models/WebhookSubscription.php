<?php

namespace Cachet\Models;

use Cachet\Database\Factories\WebhookSubscriptionFactory;
use Cachet\Enums\WebhookEventEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'selected_events' => AsEnumCollection::class . ':' . WebhookEventEnum::class,
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
    public function attempts()
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
            ->payload([
                'event' => $event->value,
                'body' => $payload,
            ])
            ->meta([
                'subscription_id' => $this->getKey(),
                'event' => $event->value,
            ])
            ->useSecret($this->secret);
    }
}
