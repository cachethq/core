<?php

namespace Cachet\Commands;

use Cachet\Models\Incident;
use Cachet\Notifications\LongRunningIncidentNotification;
use Cachet\Settings\MailSettings;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

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

        $incidents = Incident::query()
            ->unresolved()
            ->where('created_at', '<=', $threshold)
            ->whereDoesntHave('updates', fn ($query) => $query->where('created_at', '>', $threshold))
            ->with('updates')
            ->get()
            ->filter(function (Incident $incident) {
                $lastActivity = $incident->updates->max('created_at') ?? $incident->created_at;

                return $incident->long_running_notified_at === null
                    || $incident->long_running_notified_at->lt($lastActivity);
            });

        if ($incidents->isEmpty()) {
            return 0;
        }

        $incidents->each(function (Incident $incident) {
            config('cachet.user_model')::query()
                ->cursor()
                ->each(fn ($user) => $user->notify(new LongRunningIncidentNotification($incident)));

            $incident->forceFill(['long_running_notified_at' => Carbon::now()])->saveQuietly();
        });

        $this->info("Notified about {$incidents->count()} long-running incident(s).");

        return 0;
    }
}
