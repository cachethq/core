@props([
'metric',
])

@use('\Cachet\Enums\MetricViewEnum')

<div x-data="chart">
    <div class="flex flex-col gap-2">
        <div class="flex items-center gap-1.5">
            <div class="font-semibold leading-6">{{ $metric->name }}</div>

            <div x-data x-popover class="flex items-center">
                <button x-ref="anchor" x-popover:button>
                    <x-heroicon-o-question-mark-circle class="size-4 text-zinc-500 dark:text-zinc-300" />
                </button>
                <div x-popover:panel x-cloak x-transition.opacity x-anchor.right.offset.8="$refs.anchor" class="rounded bg-white px-2 py-1 text-xs font-medium text-zinc-800 drop-shadow dark:text-zinc-800">
                    <span class="pointer-events-none absolute -left-1 top-1.5 size-4 rotate-45 bg-white"></span>
                    <p class="relative">{{ $metric->description }}</p>
                </div>
            </div>

            <!-- Period Selector -->
            <select x-model="period" class="ml-auto rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 text-sm font-medium">
                @foreach([MetricViewEnum::last_hour, MetricViewEnum::today, MetricViewEnum::week, MetricViewEnum::month] as $value)
                <option value="{{ $value }}">{{ $value->getLabel() }}</option>
                @endforeach
            </select>
        </div>
        <canvas x-ref="canvas" height="300" class="ring-1 ring-gray-900/5 dark:ring-gray-100/10 bg-white dark:bg-zinc-800 rounded-md shadow-sm text-white"></canvas>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('chart', () => ({
            metric: {{ Js::from($metric) }},
            period: {{ Js::from($metric->default_view) }},
            points: [
                [],
                [],
                [],
                []
            ],
            chart: null,
            init,
        }))
    })
</script>
