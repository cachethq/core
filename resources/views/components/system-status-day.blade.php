@props([
    'date',
    'status',
    'events' => [],
])

@php($label = $status === null || $status === \Cachet\Enums\ComponentStatusEnum::unknown ? __('cachet::system_status.no_data') : $status->getLabel())

<span data-component="system-status-day"
      x-data="{ tooltipOpen: false }"
      @mouseenter="tooltipOpen = true"
      @mouseleave="tooltipOpen = false"
      x-ref="anchor"
      aria-label="{{ Carbon\Carbon::parse($date)->format('M j, Y') }}: {{ $label }}"
      class="relative block rounded-sm {{ match ($status) { \Cachet\Enums\ComponentStatusEnum::operational => 'bg-emerald-500', \Cachet\Enums\ComponentStatusEnum::major_outage => 'bg-red-500', null, \Cachet\Enums\ComponentStatusEnum::unknown => 'bg-zinc-100 dark:bg-zinc-800', default => 'bg-amber-400' } }}">
    <template x-teleport="body">
        <span x-show="tooltipOpen"
              x-cloak
              x-anchor.top.offset.8="$refs.anchor"
              role="tooltip"
              class="pointer-events-none z-30 w-72 max-w-[calc(100vw-2rem)] rounded-lg bg-white p-3 text-left shadow-lg ring-1 ring-zinc-900/10 dark:bg-zinc-800 dark:ring-white/15">
            <time datetime="{{ $date }}" class="block text-sm text-zinc-500 dark:text-zinc-400">{{ Carbon\Carbon::parse($date)->format('D, M j, Y') }}</time>
            <span class="mt-2 block space-y-2 text-sm font-medium text-zinc-900 dark:text-zinc-100">
                @forelse ($events as $event)
                    <span class="flex items-start gap-2">
                        @svg($event['icon'], 'mt-0.5 size-4 shrink-0 '.match ($event['status']) {
                            \Cachet\Enums\ComponentStatusEnum::operational => 'text-emerald-500',
                            \Cachet\Enums\ComponentStatusEnum::major_outage => 'text-red-500',
                            default => 'text-amber-400',
                        }, ['data-event-icon' => $event['icon'], 'aria-hidden' => 'true'])
                        <span class="min-w-0 break-words">{{ $event['label'] }}</span>
                    </span>
                @empty
                    <span class="flex items-start gap-2">
                        @svg(($status ?? \Cachet\Enums\ComponentStatusEnum::unknown)->getIcon(), 'mt-0.5 size-4 shrink-0 '.match ($status) {
                            \Cachet\Enums\ComponentStatusEnum::operational => 'text-emerald-500',
                            \Cachet\Enums\ComponentStatusEnum::major_outage => 'text-red-500',
                            null, \Cachet\Enums\ComponentStatusEnum::unknown => 'text-zinc-400',
                            default => 'text-amber-400',
                        }, ['aria-hidden' => 'true'])
                        <span class="min-w-0 break-words">
                            {{ match (true) {
                                $status === null, $status === \Cachet\Enums\ComponentStatusEnum::unknown => __('cachet::system_status.no_data_description'),
                                $status === \Cachet\Enums\ComponentStatusEnum::operational => __('cachet::system_status.no_activity'),
                                default => __('cachet::system_status.status_continued', ['status' => $label]),
                            } }}
                        </span>
                    </span>
                @endforelse
            </span>
        </span>
    </template>
</span>
