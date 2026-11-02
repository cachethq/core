<section data-component="schedules" class="flex flex-col gap-4">
    <div data-slot="header" class="flex items-center justify-between gap-3">
        <h2 data-slot="title" class="text-2xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ __('cachet::schedule.planned_maintenance_header') }}</h2>

        <a data-slot="rss" href="{{ route('cachet.rss.maintenance') }}" title="{{ __('cachet::cachet.rss_maintenance_feed') }}" aria-label="{{ __('cachet::cachet.rss_maintenance_feed') }}" class="text-zinc-500 transition hover:text-accent-content dark:text-zinc-400">
            @svg('cachet-rss', 'size-5')
        </a>
    </div>

    <div data-slot="content" class="group relative overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-zinc-900/10 dark:bg-zinc-900 dark:ring-white/15">
        <ul data-slot="list" class="divide-y divide-zinc-900/10 dark:divide-white/15">
            @foreach($schedules as $schedule)
                <x-cachet::schedule :schedule="$schedule" />
            @endforeach
        </ul>
    </div>
</section>
