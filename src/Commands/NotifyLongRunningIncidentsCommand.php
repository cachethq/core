<?php

namespace Cachet\Commands;

use Cachet\Models\Incident;
use Cachet\Notifications\LongRunningIncidentNotification;
use Cachet\Settings\MailSettings;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotifyLongRunningIncidentsCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'cachet:notify-long-running-incidents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify dashboard users of unresolved incidents without recent updates';

    /**
     * Execute the console command.
     */
    public function handle(MailSettings $settings): int
    {
        if (! $settings->notify_long_running_incidents) {
            return 0;
        }

        $threshold = now()->subHours($settings->long_running_incident_hours);

        $notifiedIncidents = 0;

        Incident::query()
            ->unresolved()
            ->where('created_at', '<=', $threshold)
            ->whereDoesntHave('updates', fn ($query) => $query->where('created_at', '>', $threshold))
            ->withMax('updates as latest_update_at', 'created_at')
            ->lazyById()
            ->filter(function (Incident $incident) {
                $lastActivity = $incident->getAttribute('latest_update_at') === null
                    ? $incident->created_at
                    : Carbon::parse($incident->getAttribute('latest_update_at'));

                return $incident->long_running_notified_at === null
                    || $incident->long_running_notified_at->lt($lastActivity);
            })
            ->each(function (Incident $incident) use (&$notifiedIncidents) {
                config('cachet.user_model')::query()
                    ->cursor()
                    ->each(fn ($user) => $user->notify(new LongRunningIncidentNotification($incident)));

                $incident->forceFill(['long_running_notified_at' => now()])->saveQuietly();
                $notifiedIncidents++;
            });

        if ($notifiedIncidents === 0) {
            return 0;
        }

        $this->info("Notified about {$notifiedIncidents} long-running incident(s).");

        return 0;
    }
}
