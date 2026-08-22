<div class="flex flex-col gap-4">
    <div class="flex flex-col justify-between gap-3 md:flex-row md:items-center md:gap-0">
        <h2 class="text-2xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">
            {{ $recentIncidentsOnly ? __('cachet::incident.timeline.recent_incidents_header') : __('cachet::incident.timeline.past_incidents_header') }}
        </h2>

        <fieldset aria-label="{{ __('cachet::incident.timeline.date_range_label') }}" class="grid grid-cols-2 gap-3 text-sm text-zinc-500 dark:text-zinc-400">
            <label class="flex flex-col gap-1">
                <span class="text-xs font-medium">{{ __('cachet::incident.timeline.from_label') }}</span>
                <x-filament::input.wrapper disabled>
                    <x-filament::input
                        type="date"
                        wire:model="date"
                        value="{{ $to }}"
                        disabled
                    />
                </x-filament::input.wrapper>
            </label>
            <label class="flex flex-col gap-1">
                <span class="text-xs font-medium">{{ __('cachet::incident.timeline.to_label') }}</span>
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
            </label>
        </fieldset>
    </div>

    <div class="flex w-full flex-col gap-8">
        @if ($stickiedIncidents->isNotEmpty())
            <x-cachet::incident :date="$from" :incidents="$stickiedIncidents" :with-date="false" />
        @endif

        @forelse ($timeline as $date => $day)
            <x-cachet::incident :date="$date" :incidents="$day['incidents']" :schedules="$day['schedules']" />
        @empty
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                {{ __('cachet::incident.timeline.no_incidents_reported_between', ['from' => $from, 'to' => $to]) }}
            </p>
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
