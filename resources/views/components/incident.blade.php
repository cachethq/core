@use('Cachet\Enums\IncidentStatusEnum')
@props([
    'date',
    'incidents',
])

{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_INCIDENTS_BEFORE) }}
<div class="relative flex flex-col gap-5" x-data="{ forDate: new Date(@js($date)) }">
    <div class="flex items-center gap-3">
        <div aria-hidden="true" class="h-px flex-1 bg-gradient-to-r from-transparent via-zinc-900/5 to-zinc-900/15 dark:via-white/5 dark:to-white/15"></div>
        <h3 class="inline-flex items-center gap-2 text-lg font-semibold tracking-tight text-zinc-800 dark:text-zinc-200">
            <x-heroicon-m-calendar class="size-5 text-zinc-400 dark:text-zinc-500" />
            <time datetime="{{ $date }}" x-text="forDate.toLocaleDateString(@if($appSettings->timezone !== '-')undefined, {timeZone: '{{$appSettings->timezone}}'}@endif )"></time>
        </h3>
        <div aria-hidden="true" class="h-px flex-1 bg-gradient-to-r from-zinc-900/15 via-zinc-900/5 to-transparent dark:from-white/15 dark:via-white/5"></div>
    </div>

    @forelse($incidents as $incident)
        <div x-data="{ timestamp: new Date(@js($incident->timestamp)) }"
             class="group relative rounded-lg bg-white shadow-sm ring-1 ring-zinc-900/10 dark:bg-zinc-900 dark:ring-white/15">
            <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-accent/40 to-transparent" aria-hidden="true"></div>

            <div @class([
                'flex flex-col gap-2 p-4 sm:p-6',
                'border-b border-zinc-900/10 dark:border-white/15' => $incident->updates->isNotEmpty(),
            ])>
                @if ($incident->components()->exists())
                    <div class="text-[11px] font-medium uppercase tracking-[0.08em] text-zinc-500 dark:text-zinc-400">
                        {{ $incident->components->pluck('name')->join(', ', ' and ') }}
                    </div>
                @endif

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
                            {{ $incident->timestamp->diffForHumans() }} <span class="text-zinc-300 dark:text-zinc-600">·</span> <time datetime="{{ $incident->timestamp->toW3cString() }}" x-text="timestamp.toLocaleString(@if($appSettings->timezone !== '-')undefined, {timeZone: '{{$appSettings->timezone}}'}@endif )"></time>
                        </span>
                    </div>
                    <div class="flex justify-start sm:justify-end">
                        <x-cachet::badge :status="$incident->latestStatus" />
                    </div>
                </div>

                @if ($incident->updates->isEmpty() && $incident->formattedMessage())
                    <div class="prose-sm md:prose prose-zinc dark:prose-invert prose-a:text-accent-content prose-a:underline prose-p:leading-normal mt-2">{!! $incident->formattedMessage() !!}</div>
                @endif
            </div>

            @if ($incident->updates->isNotEmpty())
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 -left-9 hidden lg:block" aria-hidden="true">
                        <div class="ml-3.5 h-full border-l-2 border-dashed border-zinc-200 dark:border-zinc-700"></div>
                        <div class="absolute inset-x-0 top-0 h-24 w-full bg-linear-to-t from-transparent to-[rgb(var(--accent-background))]"></div>
                        <div class="absolute inset-x-0 bottom-0 h-24 w-full bg-linear-to-b from-transparent to-[rgb(var(--accent-background))]"></div>
                    </div>
                    <div class="flex flex-col divide-y divide-zinc-900/10 px-4 dark:divide-white/15 sm:px-6">
                        @foreach ($incident->updates as $update)
                            <div class="relative py-5" x-data="{ timestamp: new Date(@js($update->created_at)) }">
                                <x-cachet::incident-update-status :status="$update->status" />
                                <h3 class="text-base font-semibold tracking-tight text-zinc-900 dark:text-zinc-100 sm:text-lg">{{ $update->status->getLabel() }}</h3>
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $update->created_at->diffForHumans() }} <span class="text-zinc-300 dark:text-zinc-600">·</span> <time datetime="{{ $update->created_at->toW3cString() }}" x-text="timestamp.toLocaleString(@if($appSettings->timezone !== '-')undefined, {timeZone: '{{$appSettings->timezone}}'}@endif )"></time>
                                </span>
                                <div class="prose-sm md:prose prose-zinc dark:prose-invert prose-a:text-accent-content prose-a:underline prose-p:leading-normal mt-2">{!! $update->formattedMessage() !!}</div>
                            </div>
                        @endforeach
                        <div class="relative py-5" x-data="{ timestamp: new Date(@js($incident->timestamp)) }">
                            <x-cachet::incident-update-status :status="IncidentStatusEnum::unknown" />

                            <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $incident->timestamp->diffForHumans() }} <span class="text-zinc-300 dark:text-zinc-600">·</span> <time datetime="{{ $incident->timestamp->toW3cString() }}" x-text="timestamp.toLocaleString(@if($appSettings->timezone !== '-')undefined, {timeZone: '{{$appSettings->timezone}}'}@endif )"></time>
                            </span>
                            <div class="prose-sm md:prose prose-zinc dark:prose-invert prose-a:text-accent-content prose-a:underline prose-p:leading-normal mt-2">{!! $incident->formattedMessage() !!}</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @empty
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-zinc-900/10 dark:bg-zinc-900 dark:ring-white/15 sm:p-6">
            <div class="prose-sm md:prose prose-zinc dark:prose-invert prose-a:text-accent-content prose-a:underline prose-p:leading-normal">
                {{ __('cachet::incident.no_incidents_reported') }}
            </div>
        </div>
    @endforelse
</div>
{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_INCIDENTS_AFTER) }}
