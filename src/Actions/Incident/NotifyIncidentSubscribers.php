<?php

namespace Cachet\Actions\Incident;

use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\Incident;
use Cachet\Models\Subscriber;
use Cachet\Notifications\NewIncidentNotification;
use Cachet\Settings\MailSettings;

class NotifyIncidentSubscribers
{
    public function __construct(private MailSettings $mailSettings)
    {
        //
    }

    /**
     * Queue notifications to the subscribers who should hear about the incident.
     *
     * Call after the incident's components have been attached, so that
     * component-specific subscribers are matched correctly.
     */
    public function handle(Incident $incident): void
    {
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
            ->each(fn (Subscriber $subscriber) => $subscriber->notify(new NewIncidentNotification($incident)));
    }
}
