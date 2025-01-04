<?php

namespace Cachet\Models;

use Cachet\Enums\WebhookEventEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;

class WebhookAttempt extends Model
{
    use MassPrunable, HasFactory;

    protected $fillable = [
        'subscription_id',
        'event',
        'attempt',
        'payload',
        'response_code',
        'transfer_time',
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function (self $model) {
            $model->subscription->recalculateSuccessRate()->save();
        });
    }

    protected $casts = [
        'payload' => 'json',
        'event' => WebhookEventEnum::class,
    ];

    public function subscription()
    {
        return $this->belongsTo(WebhookSubscription::class, 'subscription_id');
    }

    public function isSuccess()
    {
        return $this->response_code >= 200 && $this->response_code < 300;
    }

    public function scopeWhereSuccessful(Builder $builder)
    {
        return $builder->where('response_code', '>=', 200)->where('response_code', '<', 300);
    }

    public function prunable()
    {
        return self::where('created_at', '<', now()->subDays(
            config('cachet.webhooks.logs.prune_logs_after_days', 30)
        ));
    }
}
