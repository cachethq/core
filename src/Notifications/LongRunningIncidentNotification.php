<?php

namespace Cachet\Notifications;

use Cachet\Cachet;
use Cachet\Filament\Resources\Incidents\IncidentResource;
use Cachet\Models\Incident;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class LongRunningIncidentNotification extends Notification implements ShouldQueue
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
    public function __construct(public Incident $incident)
    {
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
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('cachet::incident.mail.long_running.subject', ['incident' => $this->incident->name]))
            ->theme(Cachet::MAIL_THEME)
            ->markdown('cachet::mail.incidents.long-running', [
                'incident' => $this->incident,
                'manageUrl' => IncidentResource::getUrl('edit', ['record' => $this->incident], panel: 'cachet'),
            ]);
    }
}
