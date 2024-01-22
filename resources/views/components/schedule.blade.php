<li class="p-4">
    <div class="flex flex-col-reverse items-start justify-between gap-4 md:flex-row md:items-center">
        <div class="flex items-start gap-2.5">
            <span class="mt-1.5 h-3 w-3 shrink-0 rounded-full bg-orange-200 animate-pulse"></span>
            <div>
                <h3 class="font-semibold leading-6">{{ $schedule->name }}</h3>
                <div class="mt-1 text-sm text-zinc-500">
                    {!! $schedule->formattedMessage() !!}
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-1 text-xs text-zinc-500 md:flex-col md:flex-nowrap md:items-end md:text-right md:text-sm">
            <span class="flex items-center gap-2">
                @svg('cachet-clock', 'h-5 w-5')
                <span>{{ $schedule->scheduled_at->diffForHumans() }}</span>
            </span>
            <span class="md:hidden">&middot;</span>
            <span>{{ $schedule->scheduled_at }}</span>
        </div>
    </div>
</li>
