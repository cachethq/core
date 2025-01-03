<div class="overflow-hidden rounded-lg border dark:border-zinc-700">
    <div class="flex items-center justify-between border-b bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-white/5">
        <h3 class="font-semibold">{{ __('cachet::schedule.planned_maintenance_header') }}</h3>
    </div>

    <div class="flex flex-col divide-y bg-white dark:bg-white/5">
        <ul class="divide-y">
            @foreach($schedules as $schedule)
            <x-cachet::schedule :schedule="$schedule" />
            @endforeach
        </ul>
    </div>
</div>
