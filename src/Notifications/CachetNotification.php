<?php

namespace Cachet\Notifications;

use Cachet\Cachet;
use Cachet\Settings\MailSettings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

abstract class CachetNotification extends Notification implements ShouldQueue
{
    /**
     * Build a message with Cachet's mail settings without changing host defaults.
     */
    protected function mailMessage(): MailMessage
    {
        $message = (new MailMessage)->theme(Cachet::MAIL_THEME);

        rescue(function () use ($message): void {
            $settings = app(MailSettings::class);

            if ($settings->from_address !== null) {
                $message->from($settings->from_address, $settings->from_name);
            }

            if (! $settings->configured()) {
                return;
            }

            config()->set('mail.mailers.cachet', $settings->toMailerConfig());
            $message->mailer('cachet');
        }, report: false);

        return $message;
    }
}
