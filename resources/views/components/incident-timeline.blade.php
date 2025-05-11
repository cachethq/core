<div class="flex flex-col gap-8">
    <div class="md:border-b py-2 dark:border-zinc-700 flex justify-between flex-col md:flex-row md:items-center gap-2 md:gap-0">
        <div>
            <h2 class="text-2xl font-semibold">
                {{ $recentIncidentsOnly ? __('cachet::incident.timeline.recent_incidents_header') : __('cachet::incident.timeline.past_incidents_header') }}</h2>
        </div>

        <div class="flex
                items-center justify-center gap-2 text-sm text-zinc-500 dark:text-zinc-400"
            x-data="{ from: new Date(@js($from)), to: new Date(@js($to)) }">

            <x-filament::input.wrapper disabled>
                <x-filament::input
                    type="date"
                    wire:model="date"
                    value="{{ $to }}"
                    disabled
                />
            </x-filament::input.wrapper>
            &mdash;
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

    <div class="flex flex-col gap-14 w-full">
        @forelse ($incidents as $date => $incident)
            <x-cachet::incident :date="$date" :incidents="$incident" />
        @empty
            <div class="text-zinc-500 dark:text-zinc-400 text-center">
                {{ __('cachet::incident.timeline.no_incidents_reported_between', ['from' => $from, 'to' => $to]) }}
            </div>
        @endforelse
    </div>

    <div class="flex justify-between">
        @if ($canPageBackward)
            <a href="{{ route('cachet.status-page', ['from' => $nextPeriodFrom]) }}"
                class="flex items-center gap-1 border dark:border-zinc-400 py-2 px-3 rounded-lg text-zinc-500 dark:text-zinc-400 hover:underline text-sm">
                <x-heroicon-m-chevron-left class="size-5" />
                {{ __('cachet::incident.timeline.navigate.previous') }}
            </a>
        @endif

        @if($canPageForward)
        <a href="{{ route('cachet.status-page') }}" class="flex items-center gap-1 border dark:border-zinc-400 py-2 px-3 rounded-lg text-zinc-500 dark:text-zinc-400 hover:underline text-sm">
            {{ __('cachet::incident.timeline.navigate.today') }}
        </a>

        <a href="{{ route('cachet.status-page', ['from' => $nextPeriodTo]) }}" class="flex items-center gap-1 border dark:border-zinc-400 py-2 px-3 rounded-lg text-zinc-500 dark:text-zinc-400 hover:underline text-sm">
            {{ __('cachet::incident.timeline.navigate.next') }}
            <x-heroicon-m-chevron-right class="size-5" />
        </a>
        @endif
    </div>
</div>
