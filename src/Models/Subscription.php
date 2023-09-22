<?php

declare(strict_types=1);

namespace Cachet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Properties
 *
 * @property-read int $id
 * @property int $component_id
 * @property int $subscriber_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property Component $component
 * @property Subscriber $subscriber*
 */
class Subscription extends Model
{
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
}
