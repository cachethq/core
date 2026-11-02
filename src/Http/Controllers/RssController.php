<?php

namespace Cachet\Http\Controllers;

use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ScheduleStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Cachet\Models\Incident;
use Cachet\Models\Schedule;
use Cachet\Settings\AppSettings;
use Cachet\Settings\RssSettings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class RssController
{
    /**
     * Cache keys of the unfiltered feeds, cleared when incidents or maintenance change.
     */
    public const CACHE_KEY_COMBINED = 'cachet::rss-feed';

    public const CACHE_KEY_INCIDENTS = 'cachet::rss-feed-incidents';

    public const CACHE_KEY_MAINTENANCE = 'cachet::rss-feed-maintenance';

    /**
     * Returns the combined RSS feed of incidents and maintenance.
     */
    public function __invoke(Request $request, AppSettings $appSettings, RssSettings $rssSettings): Response
    {
        return $this->feed($request, $appSettings, $rssSettings, self::CACHE_KEY_COMBINED, true, true);
    }

    /**
     * Returns the RSS feed of incidents.
     */
    public function incidents(Request $request, AppSettings $appSettings, RssSettings $rssSettings): Response
    {
        return $this->feed($request, $appSettings, $rssSettings, self::CACHE_KEY_INCIDENTS, true, false);
    }

    /**
     * Returns the RSS feed of maintenance.
     */
    public function maintenance(Request $request, AppSettings $appSettings, RssSettings $rssSettings): Response
    {
        return $this->feed($request, $appSettings, $rssSettings, self::CACHE_KEY_MAINTENANCE, false, true);
    }

    /**
     * Render and respond with a feed, optionally filtered by `components`.
     */
    private function feed(Request $request, AppSettings $appSettings, RssSettings $rssSettings, string $cacheKey, bool $withIncidents, bool $withMaintenance): Response
    {
        $componentIds = $this->componentIds($request);

        $render = fn () => view('cachet::rss', [
            'statusPageName' => $appSettings->name,
            'statusAbout' => $appSettings->about,
            'feedUrl' => $request->url(),
            'items' => $this->items(
                $withIncidents ? $this->feedIncidents($appSettings, $rssSettings, $componentIds) : collect(),
                $withMaintenance ? $this->feedSchedules($rssSettings, $componentIds) : collect(),
            ),
        ])->render();

        // Only unfiltered feeds are cached, so invalidation stays a fixed set of keys.
        if ($componentIds !== []) {
            $feed = $render();
            $lastModified = now();
        } else {
            $feed = Cache::remember($cacheKey, $rssSettings->refresh_interval, $render);
            $lastModified = Cache::remember($cacheKey.'-last-modified', $rssSettings->refresh_interval, fn () => now());
        }

        $response = response($feed)
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8')
            ->setEtag(hash('sha256', $feed))
            ->setLastModified($lastModified)
            ->setPublic()
            ->setMaxAge($rssSettings->refresh_interval);

        $response->isNotModified($request);

        return $response;
    }

    /**
     * Read component IDs from `components[]=1&components[]=2` or `components=1,2`.
     *
     * @return list<int>
     */
    private function componentIds(Request $request): array
    {
        $components = $request->query('components', []);

        if (is_string($components)) {
            $components = explode(',', $components);
        }

        if (! is_array($components)) {
            return [];
        }

        return collect($components)
            ->filter(fn ($id) => is_scalar($id) && ctype_digit((string) $id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  list<int>  $componentIds
     * @return Collection<int, Incident>
     */
    private function feedIncidents(AppSettings $appSettings, RssSettings $rssSettings, array $componentIds): Collection
    {
        $statuses = $rssSettings->incidentStatuses();

        if ($statuses === []) {
            return collect();
        }

        return Incident::query()
            ->viewableBy(false)
            // Incidents without a status count as "unknown".
            ->where(function ($query) use ($statuses) {
                $query->whereIn('status', $statuses);

                if (in_array(IncidentStatusEnum::unknown, $statuses, true)) {
                    $query->orWhereNull('status');
                }
            })
            ->with([
                'components' => $this->publicComponents(...),
                'updates' => fn ($query) => $query->orderBy('created_at')->orderBy('id'),
            ])
            ->when($componentIds !== [], fn ($query) => $query->whereHas(
                'components',
                fn ($query) => $this->publicComponents($query)->whereIn('components.id', $componentIds)
            ))
            ->when($appSettings->recent_incidents_only, function ($query) use ($appSettings) {
                $query->where(function ($query) use ($appSettings) {
                    $query->whereDate(
                        'occurred_at',
                        '>',
                        Carbon::now()->subDays($appSettings->recent_incidents_days)->format('Y-m-d')
                    )->orWhere(function ($query) use ($appSettings) {
                        $query->whereNull('occurred_at')->whereDate(
                            'created_at',
                            '>',
                            Carbon::now()->subDays($appSettings->recent_incidents_days)->format('Y-m-d')
                        );
                    });
                });
            })
            ->orderByDesc('created_at')
            ->limit($rssSettings->max_items)
            ->get();
    }

    /**
     * @param  list<int>  $componentIds
     * @return Collection<int, Schedule>
     */
    private function feedSchedules(RssSettings $rssSettings, array $componentIds): Collection
    {
        $statuses = $rssSettings->maintenanceStatuses();

        if ($statuses === []) {
            return collect();
        }

        return Schedule::query()
            ->published()
            ->where(function ($query) use ($statuses) {
                $now = Carbon::now();

                // Mirrors Schedule::status().
                foreach ($statuses as $status) {
                    match ($status) {
                        ScheduleStatusEnum::upcoming => $query->orWhere('scheduled_at', '>', $now),
                        ScheduleStatusEnum::in_progress => $query->orWhere(fn ($query) => $query
                            ->where(fn ($query) => $query->whereNull('scheduled_at')->orWhere('scheduled_at', '<=', $now))
                            ->where(fn ($query) => $query->whereNull('completed_at')->orWhere('completed_at', '>=', $now))),
                        ScheduleStatusEnum::complete => $query->orWhere(fn ($query) => $query
                            ->where(fn ($query) => $query->whereNull('scheduled_at')->orWhere('scheduled_at', '<=', $now))
                            ->where('completed_at', '<', $now)),
                    };
                }
            })
            ->with([
                'components' => $this->publicComponents(...),
                'updates' => fn ($query) => $query->orderBy('created_at')->orderBy('id'),
            ])
            ->when($componentIds !== [], fn ($query) => $query->whereHas(
                'components',
                fn ($query) => $this->publicComponents($query)->whereIn('components.id', $componentIds)
            ))
            ->orderByDesc('scheduled_at')
            ->limit($rssSettings->max_items)
            ->get();
    }

    /**
     * Limit components to those a guest can see: enabled, and ungrouped or in a guest-visible group.
     *
     * @param  Builder<Component>|Relation<Component, *, *>  $query
     */
    private function publicComponents(Builder|Relation $query): Builder|Relation
    {
        return $query->enabled()->where(function ($query) {
            $query->whereNull('components.component_group_id')
                ->orWhereIn('components.component_group_id', ComponentGroup::query()->guests()->select('id'));
        });
    }

    /**
     * Merge incidents and schedules into one feed, newest first.
     *
     * @param  Collection<int, Incident>  $incidents
     * @param  Collection<int, Schedule>  $schedules
     * @return Collection<int, array{model: Incident|Schedule, publishedAt: Carbon}>
     */
    private function items(Collection $incidents, Collection $schedules): Collection
    {
        return $incidents->toBase()
            ->merge($schedules->toBase())
            ->map(function (Incident|Schedule $model) {
                $publishedAt = $model->updates->last()?->updated_at
                    ?? ($model instanceof Schedule ? $model->scheduled_at : $model->created_at)
                    ?? $model->created_at;

                return ['model' => $model, 'publishedAt' => $publishedAt];
            })
            ->sortByDesc(fn (array $item) => $item['publishedAt']->getTimestamp())
            ->values();
    }
}
