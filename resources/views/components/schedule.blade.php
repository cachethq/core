{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_SCHEDULES_BEFORE) }}
<li class="px-4 py-4 sm:px-6 sm:py-6" x-data="{ timestamp: new Date(@js($schedule->scheduled_at)) }">
    <div class="flex flex-col gap-3">
        @if ($schedule->components->isNotEmpty())
            <div class="text-[11px] font-medium uppercase tracking-[0.08em] text-zinc-500 dark:text-zinc-400">
                {{ __('Affected Components') }}: {{ $schedule->components->pluck('name')->join(', ', ' and ') }}
            </div>
        @endif

        <div class="flex flex-col-reverse items-start justify-between gap-3 sm:flex-row sm:items-center">
            <div class="flex flex-1 flex-col gap-1">
                <h3 class="max-w-full break-words text-base font-semibold tracking-tight text-zinc-900 dark:text-zinc-100 sm:text-lg">
                    <a href="{{ route('cachet.status-page.schedule', ['schedule' => $schedule]) }}" class="transition hover:text-accent-content">
                        {{ $schedule->name }}
                    </a>
                </h3>
                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                    {{ $schedule->scheduled_at->diffForHumans() }} <span class="text-zinc-300 dark:text-zinc-600">·</span> <time datetime="{{ $schedule->scheduled_at->toW3cString()}}" x-text="timestamp.toLocaleString(@if($appSettings->timezone !== '-')undefined, {timeZone: '{{$appSettings->timezone}}'}@endif )"></time>
                </span>
            </div>

            <div class="flex justify-start sm:justify-end">
                <x-cachet::badge :status="$schedule->status" />
            </div>
        </div>

        <div class="prose-sm md:prose prose-zinc dark:prose-invert prose-a:text-accent-content prose-a:underline prose-p:leading-normal">{!! $schedule->formattedMessage() !!}</div>

        @if ($schedule->updates->isNotEmpty())
            <div class="flex flex-col divide-y divide-zinc-900/10 dark:divide-white/15">
                @foreach ($schedule->updates as $update)
                    <div class="py-4 first:pt-3 last:pb-0" x-data="{ timestamp: new Date(@js($update->created_at)) }">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">
                            {{ $update->created_at->diffForHumans() }} <span class="text-zinc-300 dark:text-zinc-600">·</span> <time datetime="{{ $update->created_at->toW3cString() }}" x-text="timestamp.toLocaleString(@if($appSettings->timezone !== '-')undefined, {timeZone: '{{$appSettings->timezone}}'}@endif )"></time>
                        </span>
                        <div class="prose-sm md:prose prose-zinc dark:prose-invert prose-a:text-accent-content prose-a:underline prose-p:leading-normal mt-1">{!! $update->formattedMessage() !!}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</li>
{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_SCHEDULES_AFTER) }}
