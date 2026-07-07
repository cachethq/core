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
    </div>

    <x-cachet::footer />
</x-cachet::cachet>
