<?php

namespace Cachet\Commands;

use Cachet\Actions\Schedule\NotifyScheduleCompletedSubscribers;
use Cachet\Models\Schedule;
use Cachet\Settings\MailSettings;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class NotifyCompletedSchedulesCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'cachet:notify-completed-schedules';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify subscribers of scheduled maintenance that has completed';

    /**
     * Execute the console command.
     */
    public function handle(MailSettings $settings, NotifyScheduleCompletedSubscribers $notifyScheduleCompletedSubscribers): int
    {
        if (! $settings->allow_subscribers) {
            return 0;
        }

        Schedule::query()
            ->where('notifications', true)
            ->whereNotNull('completed_at')
            ->where('completed_at', '<=', now())
            ->where(fn (Builder $query) => $query
                ->whereNull('completed_notified_at')
                ->orWhereColumn('completed_notified_at', '<', 'completed_at'))
            ->each(fn (Schedule $schedule) => $notifyScheduleCompletedSubscribers->handle($schedule));

        return 0;
    }
}
