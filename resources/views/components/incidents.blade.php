<div class="flex flex-col gap-6">
    <div class="border-b py-2 dark:border-zinc-700">
        <h2 class="text-2xl font-semibold">Past Incidents</h2>
    </div>

    <div class="flex flex-col gap-14 w-full">
        @foreach ($incidents as $date => $incident)
        <x-cachet::incident :date="$date" :incidents="$incident" />
        @endforeach
    </div>
</div>
