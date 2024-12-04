<div class="flex flex-col gap-8">
    <div class="border-b py-2 dark:border-zinc-700 flex justify-between flex-col md:flex-row md:items-center">
        <div>
            <h2 class="text-2xl font-semibold">{{ __('Past Incidents') }}</h2>
        </div>

        <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400" x-data="{ from: new Date(@js($from)), to: new Date(@js($to)) }">
            <x-filament::input.wrapper>
                <x-filament::input
                    type="date"
                    wire:model="date"
                    value="{{ $from }}"
                    x-data="{ date : '{{ $from }}' }"
                    x-model="date"
                    x-init="$watch('date', value => window.location = '?from=' + date)"
                    max="{{ now()->toDateString() }}"
                />
            </x-filament::input.wrapper>
            &mdash;
            <x-filament::input.wrapper disabled>
                <x-filament::input
                    type="date"
                    wire:model="date"
                    value="{{ $to }}"
                    disabled
                />
            </x-filament::input.wrapper>
        </div>
    </div>

    <div class="flex flex-col gap-14 w-full">
            @foreach ($incidents as $date => $incident)
            <div class="relative flex flex-col gap-5" x-data="{ forDate: new Date(@js($date)) }">
                <h3 class="text-xl font-semibold"><time datetime="{{ $date }}" x-text="forDate.toLocaleDateString()"></time></h3>
                <x-cachet::incident :date="$date" :incidents="$incident"  />
                <x-cachet::schedule-timeline :date="$date" :schedules="$schedules[$date]" />
                @if(count($incident) === 0 && count($schedules[$date]) === 0)
                    <div class="bg-white border divide-y rounded-lg ml-9 dark:divide-zinc-700 dark:border-zinc-700 dark:bg-zinc-800">
                        <div class="flex flex-col p-4 divide-y dark:divide-zinc-700">
                            <div class="prose-sm md:prose prose-zinc dark:prose-invert prose-a:text-primary-500 prose-a:underline prose-p:leading-normal">
                                {{ __('No incidents reported.') }}
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <div class="flex justify-between">
        <a href="{{ route('cachet.status-page', ['from' => $nextPeriodFrom]) }}" class="flex items-center gap-1 border dark:border-zinc-400 py-2 px-3 rounded-lg text-zinc-500 dark:text-zinc-400 hover:underline text-sm">
            <x-heroicon-m-chevron-left class="size-5" />
            {{ __('Previous') }}
        </a>

        @if($canPageForward)
        <a href="{{ route('cachet.status-page') }}" class="flex items-center gap-1 border dark:border-zinc-400 py-2 px-3 rounded-lg text-zinc-500 dark:text-zinc-400 hover:underline text-sm">
            {{ __('Today') }}
        </a>

        <a href="{{ route('cachet.status-page', ['from' => $nextPeriodTo]) }}" class="flex items-center gap-1 border dark:border-zinc-400 py-2 px-3 rounded-lg text-zinc-500 dark:text-zinc-400 hover:underline text-sm">
            {{ __('Next') }}
            <x-heroicon-m-chevron-right class="size-5" />
        </a>
        @endif
    </div>
</div>
