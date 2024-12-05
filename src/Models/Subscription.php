<?php

namespace Cachet\Models;

use Cachet\Database\Factories\SubscriptionFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $subscriber_id
 * @property int $component_od
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property Subscriber $subscriber
 * @property Component $component
 *
 * @method static SubscriptionFactory factory($count = null, $state = [])
 */
class Subscription extends Model
{
    /** @use HasFactory<SubscriptionFactory> */
    use HasFactory;

    /**
     * Get the component the subscription is for.
     */
    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class);
    }

    /**
     * Get the subscriber of the subscription.
     */
    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return SubscriptionFactory::new();
    }
}
