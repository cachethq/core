<?php

namespace Cachet\Actions\Update;

use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\Incident;
use Cachet\Models\Subscriber;
use Cachet\Models\Update;
use Cachet\Notifications\IncidentUpdatedNotification;
use Cachet\Settings\MailSettings;

class NotifyIncidentUpdateSubscribers
{
    public function __construct(private MailSettings $mailSettings)
    {
        //
    }

    /**
     * Queue notifications to the subscribers who should hear about the incident update.
     */
    public function handle(Update $update): void
    {
        $incident = $update->updateable;

        if (! $incident instanceof Incident) {
            return;
        }

        if (! $this->mailSettings->allow_subscribers) {
            return;
        }

        if (! $incident->notifications) {
            return;
        }

        if ($incident->visible !== ResourceVisibilityEnum::guest) {
            return;
        }

        Subscriber::query()
            ->verified()
            ->subscribedTo($incident)
            ->cursor()
            ->each(fn (Subscriber $subscriber) => $subscriber->notify(new IncidentUpdatedNotification($update)));
    }
}
