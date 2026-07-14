<?php

namespace Cachet\Models;

use Cachet\Concerns\HasMeta;
use Cachet\Concerns\Metable;
use Cachet\Database\Factories\SubscriberFactory;
use Cachet\Events\Subscribers\SubscriberCreated;
use Cachet\Events\Subscribers\SubscriberUnsubscribed;
use Cachet\Events\Subscribers\SubscriberVerified;
use Cachet\Notifications\VerifySubscriberEmail;
use Carbon\Carbon;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;

/**
 * @property int $id
 * @property ?string $email
 * @property ?Carbon $email_verified_at
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property int $global
 * @property ?string $phone_number
 * @property ?string $slack_webhook_url
 * @property Collection<int, Component> $components
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Meta> $meta
 */
class Subscriber extends Model implements Metable, MustVerifyEmailContract
{
    /** @use HasFactory<SubscriberFactory> */
    use HasFactory, HasMeta, MustVerifyEmail, Notifiable;

    /** @var array<string, string> */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /** @var array<string, class-string> */
    protected $dispatchesEvents = [
        'created' => SubscriberCreated::class,
        'deleted' => SubscriberUnsubscribed::class,
    ];

    /** @var list<string> */
    protected $fillable = [
        'email',
        'global',
        'email_verified_at',
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
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifySubscriberEmail);
    }

    /**
     * Scope the query to verified subscribers.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Scope the query to subscribers who should hear about the given resource.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeSubscribedTo(Builder $query, Incident|Schedule $resource): Builder
    {
        return $query->where(fn (Builder $query) => $query
            ->where('global', true)
            ->orWhereHas('components', fn (Builder $query) => $query->whereIn(
                'components.id',
                $resource->components()->pluck('components.id'),
            )));
    }

    /**
     * The signed URL for the subscriber to unsubscribe.
     */
    public function unsubscribeUrl(): string
    {
        return URL::signedRoute('cachet.subscribers.unsubscribe', [
            'subscriber' => $this->getKey(),
            'hash' => sha1($this->email),
        ]);
    }

    /**
     * Reset the subscriber's verification status.
     */
    public function resetVerification(): void
    {
        $this->update([
            'email_verified_at' => null,
        ]);
    }

    /**
     * Verify the subscriber.
     */
    public function verify(): void
    {
        if ($this->hasVerifiedEmail()) {
            return;
        }

        $this->markEmailAsVerified();

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
