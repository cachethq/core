@props(['componentGroup' => null])

{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_COMPONENT_GROUPS_BEFORE) }}
<div x-data x-disclosure {{
    $attributes
        ->merge(
            array_filter([
                'default-open' => $componentGroup->isExpanded(),
            ]),
        )
        ->class(['group relative overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-zinc-900/10 dark:bg-zinc-900 dark:ring-white/15'])
}}>
    <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-accent/40 to-transparent" aria-hidden="true"></div>

    <button x-disclosure:button class="flex w-full items-center justify-between gap-3 bg-zinc-50 px-4 py-3 text-left text-zinc-700 transition hover:bg-zinc-100 hover:text-zinc-900 dark:bg-zinc-800/50 dark:text-zinc-200 dark:hover:bg-zinc-800 dark:hover:text-white sm:px-6 sm:py-4">
        <div class="flex items-center gap-3 min-w-0">
            <h3 class="truncate text-lg font-semibold tracking-tight">
                {{ $componentGroup->name }}
            </h3>

            @if (($incidentCount = $componentGroup->components->sum('incidents_count')) > 0)
                <span class="shrink-0 rounded-md bg-zinc-100 px-2 py-0.5 text-[11px] font-semibold uppercase tracking-[0.08em] text-zinc-700 ring-1 ring-zinc-900/10 dark:bg-zinc-800 dark:text-zinc-300 dark:ring-white/15">
                    {{ trans_choice('cachet::component_group.incident_count', $incidentCount) }}
                </span>
            @endif
        </div>

        <x-heroicon-m-chevron-up ::class="!$disclosure.isOpen && 'rotate-180'" class="size-6 shrink-0 text-zinc-400 transition dark:text-zinc-500" />
    </button>

    <div x-disclosure:panel x-collapse class="border-t border-zinc-900/10 dark:border-white/15">
        <ul class="divide-y divide-zinc-900/10 dark:divide-white/15">
            @foreach ($componentGroup->components as $component)
                <x-cachet::component :component="$component" />
            @endforeach
        </ul>
    </div>
</div>
{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_COMPONENT_GROUPS_AFTER) }}
