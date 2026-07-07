<?php

namespace Cachet\Actions\Update;

use Cachet\Models\Schedule;
use Cachet\Models\Subscriber;
use Cachet\Models\Update;
use Cachet\Notifications\ScheduleUpdatedNotification;
use Cachet\Settings\MailSettings;
use Illuminate\Support\Facades\Notification;

class NotifyScheduleUpdateSubscribers
{
    public function __construct(private MailSettings $mailSettings)
    {
        //
    }

    /**
     * Queue notifications to the subscribers who should hear about the maintenance update.
     */
    public function handle(Update $update): void
    {
        $schedule = $update->updateable;

        if (! $schedule instanceof Schedule) {
            return;
        }

        if (! $this->mailSettings->allow_subscribers) {
            return;
        }

        if (! $schedule->notifications) {
            return;
        }

        Notification::send(
            Subscriber::query()->verified()->subscribedTo($schedule)->get(),
            new ScheduleUpdatedNotification($update),
        );
    }
}
