<x-filament-widgets::widget>
    <x-filament::section>
        <div @if ($pollingInterval) wire:poll.{{ $pollingInterval }} @endif>
            @if ($totalComponents === 0)
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('cachet::dashboard.system_health.empty') }}
                    </p>

                    <x-filament::link :href="$createComponentUrl">
                        {{ __('cachet::dashboard.system_health.add_component') }}
                    </x-filament::link>
                </div>
            @else
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <x-filament::icon
                            :icon="$systemStatus->getIcon()"
                            class="h-9 w-9 shrink-0"
                            style="color: {{ $systemStatus->getColor()[500] }}"
                        />

                        <h2 class="text-lg font-semibold text-gray-950 dark:text-white">
                            {{ $systemStatus->getLabel() }}
                        </h2>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        @foreach ($statusCounts as $item)
                            <x-filament::badge :color="$item['status']->getColor()" :icon="$item['status']->getIcon()">
                                {{ $item['count'] }} {{ $item['status']->getLabel() }}
                            </x-filament::badge>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
