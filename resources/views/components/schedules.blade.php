<div class="group relative overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-zinc-900/10 dark:bg-zinc-900 dark:ring-white/15">
    <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-accent/40 to-transparent" aria-hidden="true"></div>

    <div class="flex items-center justify-between gap-3 border-b border-zinc-900/10 px-4 py-3 dark:border-white/15 sm:px-6 sm:py-4">
        <h3 class="text-lg font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ __('cachet::schedule.planned_maintenance_header') }}</h3>
    </div>

    <ul class="divide-y divide-zinc-900/10 dark:divide-white/15">
        @foreach($schedules as $schedule)
            <x-cachet::schedule :schedule="$schedule" />
        @endforeach
    </ul>
</div>
