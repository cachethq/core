<li class="p-4" x-data="{ timestamp: new Date(@js($schedule->scheduled_at)) }">
    <div class="flex flex-col-reverse items-start justify-between gap-4 md:flex-row md:items-center">
        <div class="flex items-start gap-2.5 w-full">
            <span class="mt-1.5 h-3 w-3 shrink-0 rounded-full bg-orange-200 animate-pulse"></span>
            <div class="flex-1">
                <div class="flex flex-col sm:flex-row justify-between gap-2 flex-col-reverse items-start sm:items-center">
                    <div class="flex flex-col flex-1">
                        <div class="flex items-center gap-x-1">
                            <h3 class="max-w-full text-base font-semibold break-words sm:text-xl">
                                {{ $schedule->name }}
                            </h3>
                            @if ($updates = $schedule->updates()->count())
                            <span>
                                {{ __('+:count Update', ['count' => $updates]) }}
                            </span>
                            @endif
                        </div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">
                        {{ $schedule->scheduled_at->diffForHumans() }} — <time datetime="{{ $schedule->scheduled_at->toW3cString() }}" x-text="timestamp.toLocaleString()"></time>
                    </span>
                    </div>
                    <div class="flex justify-start sm:justify-end">
                        <x-cachet::badge :status="$schedule->status" />
                    </div>
                </div>
                <div class="prose-sm md:prose prose-zinc dark:prose-invert prose-a:text-primary-500 prose-a:underline prose-p:leading-normal">{!! $schedule->formattedMessage() !!}</div>
            </div>
        </div>
    </div>
</li>
