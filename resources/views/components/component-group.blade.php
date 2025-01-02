@props(['componentGroup' => null])

<div x-data x-disclosure {{ $attributes
    ->merge(array_filter([
        'default-open' => $componentGroup->isExpanded(),
    ]))
    ->class(['overflow-hidden rounded-lg border dark:border-zinc-700'])
    }}>
    <div class="flex items-center justify-between bg-white p-4 dark:border-zinc-700 dark:bg-white/5">
        <button x-disclosure:button class="flex items-center gap-2 text-zinc-500 dark:text-zinc-300">
            <h3 class="text-lg font-semibold">
                {{ $componentGroup->name }}
            </h3>
            <x-heroicon-o-chevron-up ::class="!$disclosure.isOpen && 'rotate-180'" class="size-4 transition" />
        </button>

        @if(($incidentCount = $componentGroup->components->sum('incidents_count')) > 0)
        <span class="rounded border border-zinc-800 px-2 py-1 text-xs font-semibold text-zinc-800 dark:border-zinc-600 dark:text-zinc-400">
            {{ trans_choice('cachet::componet_group.incident_count', $incidentCount) }}
        </span>
        @endif
    </div>

    <div x-disclosure:panel x-collapse class="flex flex-col divide-y bg-white dark:bg-white/5">
        <ul class="divide-y dark:divide-zinc-700">
            @foreach($componentGroup->components as $component)
            <x-cachet::component :component="$component" />
            @endforeach
        </ul>
    </div>
</div>
