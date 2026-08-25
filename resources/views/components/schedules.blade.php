<section data-component="schedules" class="flex flex-col gap-4">
    <h2 data-slot="title" class="text-2xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ __('cachet::schedule.planned_maintenance_header') }}</h2>

    <div data-slot="content" class="group relative overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-zinc-900/10 dark:bg-zinc-900 dark:ring-white/15">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-accent/40 to-transparent" aria-hidden="true"></div>

        <ul data-slot="list" class="divide-y divide-zinc-900/10 dark:divide-white/15">
            @foreach($schedules as $schedule)
                <x-cachet::schedule :schedule="$schedule" />
            @endforeach
        </ul>
    </div>
</section>
