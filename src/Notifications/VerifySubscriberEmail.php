<?php

declare(strict_types=1);

namespace Cachet\Notifications;

use Cachet\Models\Subscriber;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

final class VerifySubscriberEmail extends VerifyEmail
{
    /** @param Subscriber $notifiable */
    protected function verificationUrl($notifiable): string
    {
        return URL::temporarySignedRoute(
            'cachet.subscribers.verify',
            Carbon::now()->addMinutes(Config::get('cachet.subscribers.verification_expiry_minutes', 60)),
            [
                'subscriber' => $notifiable->getKey(),
                'hash' => sha1($notifiable->verify_code),
            ]
        );
    }
}
