<?php

namespace Cachet\Http\Controllers;

use Cachet\Models\Incident;
use Cachet\Models\Update;
use Cachet\Settings\AppSettings;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SlackRssController
{
    /**
     * The maximum number of incident events included in the feed.
     */
    private const MAX_ITEMS = 100;

    /**
     * How long the rendered feed is cached for, in seconds.
     */
    private const CACHE_TTL = 60;

    /**
     * Returns the incident event feed consumed by Slack.
     */
    public function __invoke(Request $request, AppSettings $appSettings): Response
    {
        $feed = Cache::remember('cachet::slack-rss-feed', self::CACHE_TTL, function () use ($appSettings) {
            return view('cachet::slack-rss', [
                'statusPageName' => $appSettings->name,
                'items' => $this->items($this->feedIncidents($appSettings)),
            ])->render();
        });

        $lastModified = Cache::remember('cachet::slack-rss-feed-last-modified', self::CACHE_TTL, fn () => now());

        $response = response($feed)
            ->header('Content-Type', 'application/rss+xml')
            ->setEtag(hash('sha256', $feed))
            ->setLastModified($lastModified)
            ->setPublic()
            ->setMaxAge(self::CACHE_TTL);

        $response->isNotModified($request);

        return $response;
    }

    /**
     * Get incidents that can contribute one of the newest feed events.
     *
     * @return Collection<int, Incident>
     */
    private function feedIncidents(AppSettings $appSettings): Collection
    {
        $incidents = $this->incidentQuery($appSettings);

        $incidentIds = (clone $incidents)
            ->orderByDesc('created_at')
            ->limit(self::MAX_ITEMS)
            ->pluck('id');

        $updatedIncidentIds = Update::query()
            ->where('updateable_type', (new Incident)->getMorphClass())
            ->whereIn('updateable_id', (clone $incidents)->select('id'))
            ->orderByDesc('created_at')
            ->limit(self::MAX_ITEMS)
            ->pluck('updateable_id');

        return (clone $incidents)
            ->whereKey($incidentIds->concat($updatedIncidentIds)->unique())
            ->with([
                'components',
                'updates' => fn ($query) => $query->orderBy('created_at')->orderBy('id'),
            ])
            ->get();
    }

    /**
     * Build the public incident query shared by both candidate sets.
     *
     * @return Builder<Incident>
     */
    private function incidentQuery(AppSettings $appSettings): Builder
    {
        return Incident::query()
            ->viewableBy(false)
            ->when($appSettings->recent_incidents_only, function (Builder $query) use ($appSettings) {
                $query->where(function (Builder $query) use ($appSettings) {
                    $cutoff = Carbon::now()->subDays($appSettings->recent_incidents_days)->format('Y-m-d');

                    $query->whereDate('occurred_at', '>', $cutoff)
                        ->orWhere(function (Builder $query) use ($cutoff) {
                            $query->whereNull('occurred_at')->whereDate('created_at', '>', $cutoff);
                        });
                });
            });
    }

    /**
     * Convert incidents and their updates into one chronological event stream.
     *
     * @param  Collection<int, Incident>  $incidents
     * @return Collection<int, array{incident: Incident, update: ?Update, publishedAt: Carbon}>
     */
    private function items(Collection $incidents): Collection
    {
        /** @var Collection<int, array{incident: Incident, update: ?Update, publishedAt: Carbon}> $items */
        $items = collect();

        foreach ($incidents as $incident) {
            $items->push([
                'incident' => $incident,
                'update' => null,
                'publishedAt' => $incident->timestamp,
            ]);

            foreach ($incident->updates as $update) {
                $items->push([
                    'incident' => $incident,
                    'update' => $update,
                    'publishedAt' => $update->created_at ?? $incident->timestamp,
                ]);
            }
        }

        return $items
            ->sortByDesc('publishedAt')
            ->take(self::MAX_ITEMS)
            ->values();
    }
}
