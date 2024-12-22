<?php

namespace Cachet\Models;

use Cachet\Database\Factories\SubscriberFactory;
use Cachet\Events\Subscribers\SubscriberCreated;
use Cachet\Events\Subscribers\SubscriberUnsubscribed;
use Cachet\Events\Subscribers\SubscriberVerified;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property ?string $email
 * @property string $verify_code
 * @property ?Carbon $verified_at
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property int $global
 * @property ?string $phone_number
 * @property ?string $slack_webhook_url
 * @property Collection<int, Component> $components
 */
class Subscriber extends Model
{
    /** @use HasFactory<SubscriberFactory> */
    use HasFactory;

    /** @var array<string, string> */
    protected $casts = [
        'verified_at' => 'datetime',
    ];

    /** @var array<string, string> */
    protected $dispatchesEvents = [
        'created' => SubscriberCreated::class,
        'deleted' => SubscriberUnsubscribed::class,
    ];

    /** @var list<string> */
    protected $fillable = [
        'email',
        'global',
        'verify_code',
        'verified_at',
    ];

    /**
     * Get the subscriber's component subscriptions.
     *
     * @return BelongsToMany<Component, $this>
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

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return SubscriberFactory::new();
    }
}
