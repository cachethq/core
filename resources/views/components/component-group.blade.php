@props(['componentGroup' => null])

{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_COMPONENT_GROUPS_BEFORE) }}
@php($showComponentGroupStatus = app(\Cachet\Settings\AppSettings::class)->show_component_group_status)
<li x-data x-disclosure @if ($componentGroup->isExpanded(auth()->user())) default-open @endif>
    <button x-disclosure:button class="relative flex w-full flex-col items-start gap-2 py-4 pl-6 pr-4 text-left transition hover:bg-zinc-50/60 dark:hover:bg-white/[0.02] sm:flex-row sm:items-center sm:justify-between sm:gap-3 sm:py-5 sm:pl-6 sm:pr-6">
    <span class="absolute left-2 top-7 -translate-y-1/2 text-zinc-400 dark:text-zinc-500 sm:top-1/2">
            <x-heroicon-m-chevron-right ::class="$disclosure.isOpen && 'rotate-90'" class="size-3.5 transition" />
        </span>

        <h2 class="min-w-0 truncate font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">
            {{ $componentGroup->name }}
        </h2>

        @php($groupStatus = $componentGroup->worstComponentStatus())
        @php($openIncidentCount = $componentGroup->openIncidentCount())
        @php($showGroupStatusLabel = $showComponentGroupStatus || $openIncidentCount === 0)
        <span class="flex shrink-0 items-center gap-2 text-sm font-semibold tracking-tight {{ $groupStatus->getTextColorClasses() }}">
            @if ($showGroupStatusLabel)
                <span>{{ $groupStatus->getLabel() }}</span>
            @endif

            @if ($openIncidentCount > 0)
                @if ($showGroupStatusLabel)
                    <span aria-hidden="true" class="text-zinc-400 dark:text-zinc-500">&middot;</span>
                @endif
                <span>{{ trans_choice('cachet::component_group.incident_count', $openIncidentCount) }}</span>
            @endif
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
