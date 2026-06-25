<div class="flex flex-col gap-8">
    <div class="flex flex-col justify-between gap-3 md:flex-row md:items-center md:gap-0">
        <h2 class="text-2xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">
            {{ $recentIncidentsOnly ? __('cachet::incident.timeline.recent_incidents_header') : __('cachet::incident.timeline.past_incidents_header') }}
        </h2>

        <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400"
            x-data="{ from: new Date(@js($from)), to: new Date(@js($to)) }">

            <x-filament::input.wrapper disabled>
                <x-filament::input
                    type="date"
                    wire:model="date"
                    value="{{ $to }}"
                    disabled
                />
            </x-filament::input.wrapper>
            <span class="text-zinc-400 dark:text-zinc-500">&mdash;</span>
            <x-filament::input.wrapper :disabled="$recentIncidentsOnly">
                <x-filament::input
                    type="date"
                    wire:model="date"
                    value="{{ $from }}"
                    x-data="{ date: '{{ $from }}' }"
                    x-model="date"
                    x-init="$watch('date', value => window.location = '?from=' + date)"
                    max="{{ now()->toDateString() }}"
                    :disabled="$recentIncidentsOnly"
                />
            </x-filament::input.wrapper>
        </div>
    </div>

    <div class="flex w-full flex-col gap-8">
        @forelse ($timeline as $date => $day)
            <x-cachet::incident :date="$date" :incidents="$day['incidents']" :schedules="$day['schedules']" />
        @empty
            <div class="rounded-lg bg-white px-5 py-10 text-center text-sm text-zinc-500 shadow-sm ring-1 ring-zinc-900/10 dark:bg-zinc-900 dark:text-zinc-400 dark:ring-white/15">
                {{ __('cachet::incident.timeline.no_incidents_reported_between', ['from' => $from, 'to' => $to]) }}
            </div>
        @endforelse
    </div>

    @if ($canPageBackward || $canPageForward)
        <div class="flex justify-center">
            <div class="inline-flex items-center gap-0.5 rounded-lg bg-zinc-100 p-0.5 ring-1 ring-zinc-900/10 dark:bg-zinc-800/80 dark:ring-white/15">
                @if ($canPageBackward)
                    <a href="{{ route('cachet.status-page', ['from' => $nextPeriodFrom]) }}"
                        class="inline-flex items-center gap-1 rounded-md px-2.5 py-1 text-xs font-medium text-zinc-600 transition hover:bg-white hover:text-zinc-900 hover:shadow-sm hover:ring-1 hover:ring-zinc-900/10 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-white dark:hover:ring-white/15">
                        <x-heroicon-m-chevron-left class="size-3.5" />
                        {{ __('cachet::incident.timeline.navigate.previous') }}
                    </a>
                @endif

                @if($canPageForward)
                    <a href="{{ route('cachet.status-page') }}"
                        class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-medium text-zinc-600 transition hover:bg-white hover:text-zinc-900 hover:shadow-sm hover:ring-1 hover:ring-zinc-900/10 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-white dark:hover:ring-white/15">
                        {{ __('cachet::incident.timeline.navigate.today') }}
                    </a>

                    <a href="{{ route('cachet.status-page', ['from' => $nextPeriodTo]) }}"
                        class="inline-flex items-center gap-1 rounded-md px-2.5 py-1 text-xs font-medium text-zinc-600 transition hover:bg-white hover:text-zinc-900 hover:shadow-sm hover:ring-1 hover:ring-zinc-900/10 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-white dark:hover:ring-white/15">
                        {{ __('cachet::incident.timeline.navigate.next') }}
                        <x-heroicon-m-chevron-right class="size-3.5" />
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>
