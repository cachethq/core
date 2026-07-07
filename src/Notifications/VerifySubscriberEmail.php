<?php

namespace Cachet\Notifications;

use Cachet\Cachet;
use Cachet\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifySubscriberEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Delete the queued notification when its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * Get the notification's delivery channels.
     *
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(Subscriber $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('cachet::subscriber.mail.verify.subject'))
            ->theme(Cachet::MAIL_THEME)
            ->markdown('cachet::mail.subscribers.verify', [
                'verificationUrl' => $this->verificationUrl($notifiable),
                'unsubscribeUrl' => $notifiable->unsubscribeUrl(),
            ]);
    }

    /**
     * Build the temporary signed verification URL for the subscriber.
     */
    protected function verificationUrl(Subscriber $subscriber): string
    {
        return URL::temporarySignedRoute(
            'cachet.subscribers.verify',
            now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'subscriber' => $subscriber->getKey(),
                'hash' => sha1($subscriber->getEmailForVerification()),
            ],
        );
    }
}
