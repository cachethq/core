<?php

namespace Cachet\Actions\Schedule;

use Cachet\Models\Schedule;
use Cachet\Models\Subscriber;
use Cachet\Notifications\NewScheduleNotification;
use Cachet\Settings\MailSettings;
use Illuminate\Support\Facades\Notification;

class NotifyScheduleSubscribers
{
    public function __construct(private MailSettings $mailSettings)
    {
        //
    }

    /**
     * Queue notifications to the subscribers who should hear about the scheduled maintenance.
     *
     * Call after the schedule's components have been attached, so that
     * component-specific subscribers are matched correctly.
     */
    public function handle(Schedule $schedule): void
    {
        if (! $this->mailSettings->allow_subscribers) {
            return;
        }

        if (! $schedule->notifications) {
            return;
        }

        Notification::send(
            Subscriber::query()->verified()->subscribedTo($schedule)->get(),
            new NewScheduleNotification($schedule),
        );
    }
}
