@props([
    'schedule',
    'headingLevel' => 3,
])

{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_SCHEDULES_BEFORE) }}
<li data-component="schedule" data-schedule-id="{{ $schedule->getKey() }}" class="px-4 py-4 sm:px-6 sm:py-6" x-data="{ timestamp: new Date(@js($schedule->scheduled_at)) }">
    <div data-slot="content" class="flex flex-col gap-3">
        <div data-slot="header" class="flex flex-col-reverse items-start justify-between gap-3 sm:flex-row sm:items-center">
            <div class="flex flex-1 flex-col gap-1">
                @if ($headingLevel === 1)
                    <h1 data-slot="title" class="max-w-full break-words text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100 sm:text-2xl">
                        {{ $schedule->name }}
                    </h1>
                @else
                    <h3 data-slot="title" class="max-w-full break-words text-base font-semibold tracking-tight text-zinc-900 dark:text-zinc-100 sm:text-lg">
                        <a href="{{ route('cachet.status-page.schedule', ['schedule' => $schedule]) }}" class="transition hover:text-accent-content">
                            {{ $schedule->name }}
                        </a>
                    </h3>
                @endif
                <span data-slot="timestamp" class="text-xs text-zinc-500 dark:text-zinc-400">
                    <x-cachet::timestamp :timestamp="$schedule->scheduled_at" />
                </span>
            </div>

            <div data-slot="status" class="flex justify-start sm:justify-end">
                <x-cachet::badge :status="$schedule->status" />
            </div>
        </div>

        <div data-slot="message" class="prose-sm md:prose prose-zinc dark:prose-invert prose-a:text-accent-content prose-a:underline prose-p:leading-normal">{!! $schedule->formattedMessage() !!}</div>

        @if ($schedule->components->isNotEmpty())
            <div data-slot="affected-components" class="text-xs text-zinc-500 dark:text-zinc-400">
                {{ __('Affected Components') }}: {{ $schedule->components->pluck('name')->join(', ', ' and ') }}
            </div>
        @endif

        @if ($schedule->updates->isNotEmpty())
            <div data-slot="updates" class="flex flex-col divide-y divide-zinc-900/10 dark:divide-white/15">
                @foreach ($schedule->updates as $update)
                    <div data-component="schedule-update" data-update-id="{{ $update->getKey() }}" class="py-4 first:pt-3 last:pb-0" x-data="{ timestamp: new Date(@js($update->created_at)) }">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">
                            <x-cachet::timestamp :timestamp="$update->created_at" />
                        </span>
                        <div data-slot="message" class="prose-sm md:prose prose-zinc dark:prose-invert prose-a:text-accent-content prose-a:underline prose-p:leading-normal mt-1">{!! $update->formattedMessage() !!}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</li>
{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_SCHEDULES_AFTER) }}
