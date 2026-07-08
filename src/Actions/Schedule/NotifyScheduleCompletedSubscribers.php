<?php

namespace Cachet\Actions\Schedule;

use Cachet\Enums\ScheduleStatusEnum;
use Cachet\Models\Schedule;
use Cachet\Models\Subscriber;
use Cachet\Notifications\ScheduleCompletedNotification;
use Cachet\Settings\MailSettings;

class NotifyScheduleCompletedSubscribers
{
    public function __construct(private MailSettings $mailSettings)
    {
        //
    }

    /**
     * Queue notifications to the subscribers who should hear that maintenance has completed.
     */
    public function handle(Schedule $schedule): void
    {
        if (! $this->mailSettings->allow_subscribers) {
            return;
        }

        if (! $schedule->notifications) {
            return;
        }

        if ($schedule->status !== ScheduleStatusEnum::complete) {
            return;
        }

        if ($schedule->completed_notified_at !== null && $schedule->completed_at->lte($schedule->completed_notified_at)) {
            return;
        }

        Subscriber::query()
            ->verified()
            ->subscribedTo($schedule)
            ->cursor()
            ->each(fn (Subscriber $subscriber) => $subscriber->notify(new ScheduleCompletedNotification($schedule)));

        $schedule->forceFill(['completed_notified_at' => now()])->saveQuietly();
    }
}
