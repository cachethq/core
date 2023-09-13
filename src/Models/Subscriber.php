<?php

namespace Cachet\Models;

use Cachet\Events\Subscribers\SubscriberCreated;
use Cachet\Events\Subscribers\SubscriberUnsubscribed;
use Cachet\Events\Subscribers\SubscriberVerified;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
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
