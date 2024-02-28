<div class="flex flex-col gap-8">
    <div class="border-b py-2 dark:border-zinc-700 flex justify-between flex-col md:flex-row md:items-center">
        <div>
            <h2 class="text-2xl font-semibold">{{ __('Past Incidents') }}</h2>
        </div>
        <div class="text-sm text-zinc-500 dark:text-zinc-400">
            {{ __('Showing :from - :to', ['from' => $from, 'to' => $to]) }}
        </div>
    </div>

    <div class="flex flex-col gap-14 w-full">
        @forelse ($incidents as $date => $incident)
        <x-cachet::incident :date="$date" :incidents="$incident" />
        @empty
        <div class="text-zinc-500 dark:text-zinc-400 text-center">
            {{ __('No incidents reported between :from and :to.', ['from' => $from, 'to' => $to]) }}
        </div>
        @endforelse
    </div>

    <div class="flex justify-between">
        <a href="{{ route('cachet.status-page', ['from' => $from]) }}" class="text-zinc-500 dark:text-zinc-400 hover:underline text-sm">
            {{ __('Previous Incidents') }}
        </a>

        @if($canPageForward)
        <a href="{{ route('cachet.status-page', ['from' => $to]) }}" class="text-zinc-500 dark:text-zinc-400 hover:underline text-sm">
            {{ __('Newer Incidents') }}
        </a>
        @endif
    </div>
</div>
