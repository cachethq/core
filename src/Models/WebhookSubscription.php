<?php

namespace Cachet\Models;

use Cachet\Enums\WebhookEventEnum;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Model;
use Spatie\WebhookServer\WebhookCall;

class WebhookSubscription extends Model
{
    protected $fillable = [
        'url',
        'secret',
        'description',
        'send_all_events',
        'selected_events',
    ];

    protected $casts = [
        'selected_events' => AsEnumCollection::class . ':' . WebhookEventEnum::class,
    ];

    public function scopeWhereEvent(WebhookEventEnum $event)
    {
        return $this->where('send_all_events', true)
            ->orWhereJsonContains('selected_events', $event->value);
    }

    public function attempts()
    {
        return $this->hasMany(WebhookAttempt::class, 'subscription_id')->latest();
    }

    public function makeCall(WebhookEventEnum $event, array $payload): WebhookCall
    {
        return WebhookCall::create()
            ->url($this->url)
            ->payload($payload)
            ->meta([
                'subscription_id' => $this->getKey(),
                'event' => $event->value,
            ])
            ->useSecret($this->secret);
    }
}
