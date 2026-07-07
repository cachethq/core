<?php

namespace Cachet\Notifications;

use Cachet\Cachet;
use Cachet\Models\Schedule;
use Cachet\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class ScheduleRescheduledNotification extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Delete the queued notification when its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Schedule $schedule,
        public ?Carbon $previousScheduledAt,
        public ?Carbon $previousCompletedAt,
    ) {
        //
    }

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
            ->subject(__('cachet::subscriber.mail.schedule_rescheduled.subject', ['schedule' => $this->schedule->name]))
            ->theme(Cachet::MAIL_THEME)
            ->markdown('cachet::mail.subscribers.schedule-rescheduled', [
                'schedule' => $this->schedule,
                'previousScheduledAt' => $this->previousScheduledAt,
                'previousCompletedAt' => $this->previousCompletedAt,
                'unsubscribeUrl' => $notifiable->unsubscribeUrl(),
            ]);
    }
}
