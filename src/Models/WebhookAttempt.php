<?php

namespace Cachet\Models;

use Cachet\Enums\WebhookEventEnum;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;

class WebhookAttempt extends Model
{
    use MassPrunable;

    protected $fillable = [
        'subscription_id',
        'event',
        'attempt',
        'payload',
        'response_code',
        'transfer_time',
    ];

    protected $casts = [
        'event' => WebhookEventEnum::class,
    ];

    public function prunable()
    {
        return self::where('created_at', '<', now()->subDays(
            config('cachet.webhooks.logs.prune_logs_after_days', 30)
        ));
    }
}
