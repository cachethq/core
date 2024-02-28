<div class="flex flex-col gap-8">
    <div class="border-b py-2 dark:border-zinc-700">
        <h2 class="text-2xl font-semibold">Past Incidents</h2>
    </div>

    <div class="flex flex-col gap-14 w-full">
        @foreach ($incidents as $date => $incident)
        <x-cachet::incident :date="$date" :incidents="$incident" />
        @endforeach
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
