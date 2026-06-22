<x-cachet::cachet :title="$incident->name">
    <x-cachet::header />

    <div class="container mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8 flex flex-col space-y-6">
        <x-cachet::status-bar />

        @if ($incident->components->isNotEmpty())
            <div class="group relative overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-zinc-900/10 dark:bg-zinc-900 dark:ring-white/15">
                <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-accent/40 to-transparent" aria-hidden="true"></div>

                <h2 class="bg-zinc-50 px-4 py-3 text-lg font-semibold tracking-tight text-zinc-700 dark:bg-zinc-800/50 dark:text-zinc-200 sm:px-6 sm:py-4">
                    {{ __('cachet::incident.affected_components_header') }}
                </h2>

                <ul class="divide-y divide-zinc-900/10 border-t border-zinc-900/10 dark:divide-white/15 dark:border-white/15">
                    @foreach ($incident->components as $component)
                        <x-cachet::component :component="$component" :hide-status="true" />
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-14 w-full">
                <x-cachet::incident :date="$incident->timestamp" :incidents="[$incident]" />
            </div>
        </div>

        <p class="text-xs text-right">
            {{ __('cachet::incident.form.guid_label') . ' ' . $incident->guid }}
        </p>
    </div>

    <x-cachet::footer />
</x-cachet::cachet>
