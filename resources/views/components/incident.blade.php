@use('Cachet\Enums\IncidentStatusEnum')
@props([
    'date',
    'incidents',
    'schedules' => [],
    'withDate' => true,
])

{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_INCIDENTS_BEFORE) }}
<div @class(['relative flex flex-col', 'gap-5' => count($incidents) > 0 || count($schedules) > 0, 'gap-2' => count($incidents) === 0 && count($schedules) === 0]) x-data="{ forDate: new Date(@js($date)) }">
    @if($withDate)
        <h3 class="border-b border-zinc-900/10 pb-2 text-base font-semibold tracking-tight text-zinc-800 dark:border-white/15 dark:text-zinc-200">
            <time datetime="{{ $date }}" x-text="forDate.toLocaleDateString(undefined, { dateStyle: 'medium'@if($appSettings->timezone !== '-'), timeZone: '{{ $appSettings->timezone }}'@endif })"></time>
        </h3>
    @endif

    @foreach($incidents as $incident)
        <div x-data="{ timestamp: new Date(@js($incident->timestamp)) }"
             class="group relative rounded-lg bg-white shadow-sm ring-1 ring-zinc-900/10 dark:bg-zinc-900 dark:ring-white/15">
            <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-accent/40 to-transparent" aria-hidden="true"></div>

            <div class="flex flex-col gap-2 border-b border-zinc-900/10 p-4 dark:border-white/15 sm:p-6">
                <div class="flex flex-col-reverse items-start justify-between gap-3 sm:flex-row sm:items-center">
                    <div class="flex flex-1 flex-col gap-1">
                        <div class="flex items-center gap-2">
                            <h3 class="max-w-full break-words text-base font-semibold tracking-tight text-zinc-900 dark:text-zinc-100 sm:text-lg">
                                <a href="{{ route('cachet.status-page.incident', $incident) }}" class="transition hover:text-accent-content">{{ $incident->name }}</a>
                            </h3>
                            @auth
                                <a href="{{ $incident->filamentDashboardEditUrl() }}"
                                   class="text-zinc-400 transition hover:text-zinc-700 dark:text-zinc-500 dark:hover:text-zinc-200"
                                   title="{{ __('cachet::incident.edit_button_title') }}">
                                    <x-heroicon-m-pencil-square class="size-4" />
                                </a>
                            @endauth
                        </div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">
                            <x-cachet::timestamp :timestamp="$incident->timestamp" />
                        </span>
                    </div>
                    <div class="flex justify-start sm:justify-end">
                        <x-cachet::badge :status="$incident->latestStatus" />
                    </div>
                </div>

                @if ($incident->components->isNotEmpty())
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                        {{ __('Affected Components') }}: {{ $incident->components->pluck('name')->join(', ', ' and ') }}
                    </div>
                @endif
            </div>

            <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 -left-9 hidden lg:block" aria-hidden="true">
                        <div class="ml-3.5 h-full border-l-2 border-dashed border-zinc-200 dark:border-zinc-700"></div>
                        <div class="absolute inset-x-0 top-0 h-24 w-full bg-linear-to-t from-transparent to-accent-background"></div>
                        <div class="absolute inset-x-0 bottom-0 h-24 w-full bg-linear-to-b from-transparent to-accent-background"></div>
                    </div>
                    <div class="flex flex-col divide-y divide-zinc-900/10 px-4 dark:divide-white/15 sm:px-6">
                        @foreach ($incident->updates as $update)
                            <div class="relative py-5 sm:last:pb-6" x-data="{ timestamp: new Date(@js($update->created_at)) }">
                                <x-cachet::incident-update-status :status="$update->status" />
                                <h3 class="text-sm font-semibold tracking-tight text-zinc-900 dark:text-zinc-100 sm:text-base">{{ $update->status->getLabel() }}</h3>
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                    <x-cachet::timestamp :timestamp="$update->created_at" />
                                </span>
                                <div class="prose-sm md:prose prose-zinc dark:prose-invert prose-a:text-accent-content prose-a:underline prose-p:leading-normal mt-2">{!! $update->formattedMessage() !!}</div>
                            </div>
                        @endforeach
                        <div class="relative py-5 sm:last:pb-6" x-data="{ timestamp: new Date(@js($incident->timestamp)) }">
                            @php($reportStatus = $incident->updates->isEmpty() ? $incident->status : null)
                            <x-cachet::incident-update-status :status="$reportStatus ?? IncidentStatusEnum::unknown" />
                            <h3 class="text-sm font-semibold tracking-tight text-zinc-900 dark:text-zinc-100 sm:text-base">{{ $reportStatus?->getLabel() ?? __('Reported') }}</h3>
                            <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                <x-cachet::timestamp :timestamp="$incident->timestamp" />
                            </span>
                            <div class="prose-sm md:prose prose-zinc dark:prose-invert prose-a:text-accent-content prose-a:underline prose-p:leading-normal mt-2">{!! $incident->formattedMessage() !!}</div>
                        </div>
                    </div>
                </div>
        </div>
    @endforeach

    @foreach($schedules as $schedule)
        <div x-data="{ timestamp: new Date(@js($schedule->completed_at)) }"
             class="group relative rounded-lg bg-white shadow-sm ring-1 ring-zinc-900/10 dark:bg-zinc-900 dark:ring-white/15">
            <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-accent/40 to-transparent" aria-hidden="true"></div>

            <div @class([
                'flex flex-col gap-2 p-4 sm:p-6',
                'border-b border-zinc-900/10 dark:border-white/15' => $schedule->updates->isNotEmpty(),
            ])>

                <div class="flex flex-col-reverse items-start justify-between gap-3 sm:flex-row sm:items-center">
                    <div class="flex flex-1 flex-col gap-1">
                        <h3 class="max-w-full break-words text-base font-semibold tracking-tight text-zinc-900 dark:text-zinc-100 sm:text-lg">
                            {{ $schedule->name }}
                        </h3>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">
                            <x-cachet::timestamp :timestamp="$schedule->completed_at" />
                        </span>
                    </div>
                    <div class="flex justify-start sm:justify-end">
                        <x-cachet::badge :status="$schedule->status" />
                    </div>
                </div>

                @if ($schedule->components->isNotEmpty())
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                        {{ __('Affected Components') }}: {{ $schedule->components->pluck('name')->join(', ', ' and ') }}
                    </div>
                @endif

                @if ($schedule->updates->isEmpty() && $schedule->formattedMessage())
                    <div class="prose-sm md:prose prose-zinc dark:prose-invert prose-a:text-accent-content prose-a:underline prose-p:leading-normal mt-2">{!! $schedule->formattedMessage() !!}</div>
                @endif
            </div>

            @if ($schedule->updates->isNotEmpty())
                <div class="flex flex-col divide-y divide-zinc-900/10 px-4 dark:divide-white/15 sm:px-6">
                    @foreach ($schedule->updates as $update)
                        <div class="relative py-5 sm:last:pb-6" x-data="{ timestamp: new Date(@js($update->created_at)) }">
                            <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                <x-cachet::timestamp :timestamp="$update->created_at" />
                            </span>
                            <div class="prose-sm md:prose prose-zinc dark:prose-invert prose-a:text-accent-content prose-a:underline prose-p:leading-normal mt-2">{!! $update->formattedMessage() !!}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach

    @if (count($incidents) === 0 && count($schedules) === 0)
        <p class="text-sm text-zinc-500 dark:text-zinc-400">
            {{ __('cachet::incident.no_incidents_reported') }}
        </p>
    @endif
</div>
{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_INCIDENTS_AFTER) }}
