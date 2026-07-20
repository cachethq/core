<x-cachet::cachet :title="$schedule->name">
    <x-cachet::header />

    <div class="container mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8 flex flex-col space-y-6">
        <x-cachet::status-bar />

        <div class="group relative overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-zinc-900/10 dark:bg-zinc-900 dark:ring-white/15">
            <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-accent/40 to-transparent" aria-hidden="true"></div>

            <ul class="divide-y divide-zinc-900/10 dark:divide-white/15">
                <x-cachet::schedule :schedule="$schedule" />
            </ul>
        </div>

        <div class="flex justify-left">
            <div class="inline-flex items-center gap-0.5 rounded-lg bg-zinc-100 p-0.5 ring-1 ring-zinc-900/10 dark:bg-zinc-800/80 dark:ring-white/15">
                <a href="{{ route('cachet.status-page') }}"
                    class="inline-flex items-center gap-1 rounded-md px-2.5 py-1 text-xs font-medium text-zinc-600 transition hover:bg-white hover:text-zinc-900 hover:shadow-sm hover:ring-1 hover:ring-zinc-900/10 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-white dark:hover:ring-white/15">
                    <x-heroicon-m-chevron-left class="size-3.5" />
                    {{ __('cachet::incident.timeline.navigate.timeline') }}
                </a>
            </div>
        </div>
    </div>

    <x-cachet::footer />
</x-cachet::cachet>
