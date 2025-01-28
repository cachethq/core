<?php

namespace Cachet\Concerns;

use Cachet\Models\Subscriber;
use Cachet\Notifications\VerifySubscriberEmail;
use Illuminate\Notifications\Notifiable;

/** @mixin Subscriber */
trait SubscriberMustVerifyEmail
{
    use Notifiable;

    /**
     * Determine if the user has verified their email address.
     */
    public function hasVerifiedEmail(): bool
    {
        return ! is_null($this->verified_at);
    }

    /**
     * Mark the given user's email as verified.
     */
    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifySubscriberEmail);
    }

    /**
     * Get the email address that should be used for verification.
     */
    public function getEmailForVerification(): string
    {
        return $this->email;
    }
}
