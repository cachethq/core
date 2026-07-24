<?php

namespace Cachet\Commands;

use Cachet\Actions\Incident\NotifyIncidentSubscribers;
use Cachet\Actions\Schedule\NotifyScheduleSubscribers;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\Incident;
use Cachet\Models\Schedule;
use Cachet\Settings\MailSettings;
use Illuminate\Console\Command;

class PublishScheduledCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'cachet:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify subscribers of scheduled incidents and maintenance that have reached their publish time';

    /**
     * Execute the console command.
     *
     * Visibility is evaluated at read time via the "published" scope, so items
     * appear the moment their publish time passes. This command only fires the
     * subscriber notifications that were deferred until publication, marking each
     * item as notified so they are announced exactly once.
     */
    public function handle(
        MailSettings $settings,
        NotifyIncidentSubscribers $notifyIncidentSubscribers,
        NotifyScheduleSubscribers $notifyScheduleSubscribers,
    ): int {
        if (! $settings->allow_subscribers) {
            return 0;
        }

        Incident::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->whereNull('published_notified_at')
            ->where('notifications', true)
            ->visibility(ResourceVisibilityEnum::guest)
            ->get()
            ->each(fn (Incident $incident) => $notifyIncidentSubscribers->handle($incident));

        Schedule::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->whereNull('published_notified_at')
            ->where('notifications', true)
            ->get()
            ->each(fn (Schedule $schedule) => $notifyScheduleSubscribers->handle($schedule));

        return 0;
    }
}
