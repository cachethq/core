<?php

namespace Cachet\Actions\Schedule;

use Cachet\Enums\ScheduleStatusEnum;
use Cachet\Models\Schedule;
use Cachet\Models\Subscriber;
use Cachet\Notifications\ScheduleRescheduledNotification;
use Cachet\Settings\MailSettings;
use Illuminate\Support\Carbon;

class NotifyScheduleRescheduledSubscribers
{
    public function __construct(private MailSettings $mailSettings)
    {
        //
    }

    /**
     * Queue notifications to the subscribers who should hear that the maintenance window moved.
     */
    public function handle(Schedule $schedule, ?Carbon $previousScheduledAt, ?Carbon $previousCompletedAt): void
    {
        if (! $this->mailSettings->allow_subscribers) {
            return;
        }

        if (! $schedule->notifications) {
            return;
        }

        if ($schedule->status === ScheduleStatusEnum::complete) {
            return;
        }

        Subscriber::query()
            ->verified()
            ->subscribedTo($schedule)
            ->cursor()
            ->each(fn (Subscriber $subscriber) => $subscriber->notify(new ScheduleRescheduledNotification($schedule, $previousScheduledAt, $previousCompletedAt)));
    }
}
