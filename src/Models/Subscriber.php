<?php

declare(strict_types=1);

namespace Cachet\Models;

use Cachet\Database\Factories\SubscriberFactory;
use Cachet\Events\Subscribers\SubscriberCreated;
use Cachet\Events\Subscribers\SubscriberUnsubscribed;
use Cachet\Events\Subscribers\SubscriberVerified;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * Properties
 *
 * @property-read int $id
 * @property string $email
 * @property ?string $verify_code
 * @property ?Carbon $verified_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property Collection<array-key, Component> $components
 *
 * Methods
 *
 * @method static SubscriberFactory factory($count = null, $state = [])
 */
class Subscriber extends Model
{
    use HasFactory;

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    protected $dispatchesEvents = [
        'created' => SubscriberCreated::class,
        'deleted' => SubscriberUnsubscribed::class,
    ];

    protected $fillable = [
        'email',
        'global',
        'verify_code',
        'verified_at',
    ];

    /**
     * Get the subscriber's component subscriptions.
     */
    public function components(): BelongsToMany
    {
        return $this->belongsToMany(Component::class, 'subscriptions');
    }

    /**
     * Reset the subscriber's verification status.
     */
    public function resetVerification(): void
    {
        $this->update([
            'verify_code' => Str::random(42),
            'verified_at' => null,
        ]);
    }

    /**
     * Verify the subscriber.
     */
    public function verify(): void
    {
        if ($this->verified_at) {
            return;
        }

        $this->update([
            'verified_at' => now(),
        ]);

        SubscriberVerified::dispatch($this);
    }
}
