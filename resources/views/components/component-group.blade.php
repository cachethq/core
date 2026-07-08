@props(['componentGroup' => null])

{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_COMPONENT_GROUPS_BEFORE) }}
@php($groupStatus = $componentGroup->worstComponentStatus())
<li x-data x-disclosure @if ($componentGroup->isExpanded()) default-open @endif>
    <button x-disclosure:button class="relative flex w-full items-center justify-between gap-3 py-3 pl-8 pr-4 text-left transition hover:bg-zinc-50/60 dark:hover:bg-white/[0.02] sm:py-4 sm:pl-9 sm:pr-6">
        <span class="absolute left-2 top-1/2 -translate-y-1/2 text-zinc-400 dark:text-zinc-500 sm:left-3">
            <x-heroicon-m-chevron-right ::class="$disclosure.isOpen && 'rotate-90'" class="size-3.5 transition" />
        </span>

        <h4 class="min-w-0 truncate font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">
            {{ $componentGroup->name }}
        </h4>

        <span class="shrink-0 text-sm font-medium {{ $groupStatus->getTextColorClasses() }}">
            {{ $groupStatus->getLabel() }}
        </span>
    </button>

    <div x-disclosure:panel x-collapse class="border-t border-zinc-900/10 dark:border-white/15">
        <ul class="divide-y divide-zinc-900/10 dark:divide-white/15">
            @foreach ($componentGroup->components as $component)
                <x-cachet::component :component="$component" :nested="true" />
            @endforeach
        </ul>
    </div>
</li>
{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_COMPONENT_GROUPS_AFTER) }}
