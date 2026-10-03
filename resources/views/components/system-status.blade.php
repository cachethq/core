{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_SYSTEM_STATUS_BEFORE) }}
<section data-component="system-status" class="status-overview">
    <header class="status-overview__masthead">
        <h2 data-slot="title" class="font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ __('cachet::system_status.title') }}</h2>
        <p class="text-sm text-zinc-500 dark:text-zinc-400">
            <time datetime="{{ $from->toDateString() }}">{{ $from->format('M j') }}</time>
            <span aria-hidden="true">–</span>
            <time datetime="{{ $to->toDateString() }}">{{ $to->format('M j, Y') }}</time>
        </p>
    </header>

    <div class="divide-y divide-zinc-900/10 dark:divide-white/15">
        @foreach ($groups as $group)
            {{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_SYSTEM_STATUS_GROUP_BEFORE) }}
            <div data-component="system-status-group" data-component-group-id="{{ $group['model']->getKey() }}" x-data x-disclosure>
                <button data-slot="trigger" x-disclosure:button class="w-full px-5 py-4 text-left transition hover:bg-zinc-50/60 dark:hover:bg-white/[0.02] sm:px-6">
                    <div class="flex items-center justify-between gap-4">
                        <span class="flex min-w-0 items-center gap-2">
                            <span x-show="! $disclosure.isOpen" class="flex shrink-0">
                                <x-heroicon-s-check-circle class="size-4 text-emerald-500" />
                            </span>
                            <span class="truncate font-semibold text-zinc-900 dark:text-zinc-100">{{ $group['model']->name }}</span>
                            <x-heroicon-m-chevron-down ::class="$disclosure.isOpen && 'rotate-180'" class="size-3.5 shrink-0 text-zinc-400 transition" />
                            <span class="hidden shrink-0 text-sm text-zinc-500 dark:text-zinc-400 lg:inline">{{ trans_choice('cachet::system_status.component_count', $group['components']->count(), ['count' => $group['components']->count()]) }}</span>
                        </span>
                        <span x-show="! $disclosure.isOpen" class="hidden shrink-0 text-sm text-zinc-500 dark:text-zinc-400 lg:inline">{{ $group['uptime'] === null ? __('cachet::system_status.no_data') : __('cachet::system_status.uptime', ['uptime' => number_format($group['uptime'], 2)]) }}</span>
                    </div>
                    <div data-slot="indicators" x-show="! $disclosure.isOpen" class="mt-3 hidden h-4 gap-0.5 lg:grid" style="grid-template-columns: repeat({{ $group['days']->count() }}, minmax(2px, 1fr))">
                        @foreach ($group['days'] as $date => $status)
                            <x-cachet::system-status-day :$date :$status :events="$group['events']->get($date, [])" />
                        @endforeach
                    </div>
                </button>

                <div data-slot="content" x-disclosure:panel x-collapse class="border-t border-zinc-900/10 bg-zinc-50/40 dark:border-white/15 dark:bg-white/[0.02]">
                    <div class="divide-y divide-zinc-900/10 dark:divide-white/15">
                        @foreach ($group['components'] as $componentRow)
                            {{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_SYSTEM_STATUS_COMPONENT_BEFORE) }}
                            <div data-component="system-status-component" data-component-id="{{ $componentRow['model']->getKey() }}" class="px-5 py-4 sm:px-6">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex min-w-0 items-center gap-2">
                                        @svg($componentRow['status']->getIcon(), 'size-4 shrink-0 '.$componentRow['status']->getTextColorClasses(), [
                                            'data-slot' => 'status-icon',
                                            'aria-label' => $componentRow['status']->getLabel(),
                                        ])
                                        <x-cachet::system-status-component-name :model="$componentRow['model']" />
                                    </div>
                                    <span class="hidden shrink-0 text-sm text-zinc-500 dark:text-zinc-400 lg:inline">{{ $componentRow['uptime'] === null ? __('cachet::system_status.no_data') : __('cachet::system_status.uptime', ['uptime' => number_format($componentRow['uptime'], 2)]) }}</span>
                                </div>
                                <div data-slot="indicators" class="mt-3 hidden h-4 gap-0.5 lg:grid" style="grid-template-columns: repeat({{ $componentRow['days']->count() }}, minmax(2px, 1fr))">
                                    @foreach ($componentRow['days'] as $date => $status)
                                        <x-cachet::system-status-day :$date :$status :events="$componentRow['events']->get($date, [])" />
                                    @endforeach
                                </div>
                            </div>
                            {{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_SYSTEM_STATUS_COMPONENT_AFTER) }}
                        @endforeach
                    </div>
                </div>
            </div>
            {{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_SYSTEM_STATUS_GROUP_AFTER) }}
        @endforeach

        @foreach ($ungroupedComponents as $componentRow)
            {{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_SYSTEM_STATUS_COMPONENT_BEFORE) }}
            <div data-component="system-status-component" data-component-id="{{ $componentRow['model']->getKey() }}" class="px-5 py-4 sm:px-6">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex min-w-0 items-center gap-2">
                        @svg($componentRow['status']->getIcon(), 'size-4 shrink-0 '.$componentRow['status']->getTextColorClasses(), [
                            'data-slot' => 'status-icon',
                            'aria-label' => $componentRow['status']->getLabel(),
                        ])
                        <x-cachet::system-status-component-name :model="$componentRow['model']" emphasized />
                    </div>
                    <span class="hidden shrink-0 text-sm text-zinc-500 dark:text-zinc-400 lg:inline">{{ $componentRow['uptime'] === null ? __('cachet::system_status.no_data') : __('cachet::system_status.uptime', ['uptime' => number_format($componentRow['uptime'], 2)]) }}</span>
                </div>
                <div data-slot="indicators" class="mt-3 hidden h-4 gap-0.5 lg:grid" style="grid-template-columns: repeat({{ $componentRow['days']->count() }}, minmax(2px, 1fr))">
                    @foreach ($componentRow['days'] as $date => $status)
                        <x-cachet::system-status-day :$date :$status :events="$componentRow['events']->get($date, [])" />
                    @endforeach
                </div>
            </div>
            {{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_SYSTEM_STATUS_COMPONENT_AFTER) }}
        @endforeach
    </div>
</section>
{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_SYSTEM_STATUS_AFTER) }}
