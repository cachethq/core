<li class="p-4">
    <div class="flex flex-col-reverse items-start justify-between gap-4 md:flex-row md:items-center">
        <div class="flex items-start gap-2.5">
            <span class="mt-1.5 h-3 w-3 shrink-0 rounded-full bg-orange-200 animate-pulse"></span>
            <div>
                <div class="flex flex-col sm:flex-row gap-x-2 gap-y">
                    <h3 class="font-semibold leading-6">{{ $schedule->name }}</h3>
                    <div class="flex grow text-sm sm:text-xs gap-2 text-gray-400 items-center">
                        @svg('cachet-clock', 'size-4 hidden sm:block')
                        <span>{{ $schedule->scheduled_at->toFormattedDayDateString() }}</span>
                    </div>
                </div>
                <div class="prose-sm md:prose-zinc mt-1">
                    {!! $schedule->formattedMessage() !!}
                </div>
            </div>
        </div>
    </div>
</li>
