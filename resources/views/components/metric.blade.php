@props([
    'metric',
])

@use('\Cachet\Enums\MetricViewEnum')

<div x-data="chart_{{ $metric->id }}"
     class="group relative overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-zinc-900/10 dark:bg-zinc-900 dark:ring-white/15">
    <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-accent/40 to-transparent" aria-hidden="true"></div>

    <div class="flex flex-col gap-4 p-4 sm:gap-5 sm:p-6">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="flex min-w-0 flex-col gap-1">
                <div class="flex items-center gap-1.5">
                    <h3 class="truncate text-lg font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">
                        {{ $metric->name }}
                    </h3>

                    @if($metric->description)
                        <div x-data x-popover class="flex shrink-0 items-center">
                            <button type="button" x-ref="anchor" x-popover:button class="flex items-center justify-center rounded-full text-zinc-400 transition hover:text-zinc-700 dark:text-zinc-500 dark:hover:text-zinc-200">
                                <x-heroicon-o-information-circle class="size-4" />
                            </button>
                            <div x-popover:panel x-cloak x-transition.opacity x-anchor.right.offset.8="$refs.anchor" class="z-10 max-w-xs rounded-md bg-zinc-900 px-2.5 py-1.5 text-xs font-medium text-white shadow-lg dark:bg-zinc-100 dark:text-zinc-900">
                                <span class="pointer-events-none absolute -left-1 top-2 size-2 rotate-45 bg-zinc-900 dark:bg-zinc-100" aria-hidden="true"></span>
                                <p class="relative">{{ $metric->description }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                @if($metric->suffix)
                    <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">
                        {{ $metric->suffix }}
                    </div>
                @endif
            </div>

            <div role="tablist" aria-label="{{ __('cachet::metric.overview.metric_points_label') }}" class="inline-flex shrink-0 items-center gap-0.5 rounded-lg bg-zinc-100 p-0.5 ring-1 ring-zinc-900/10 dark:bg-zinc-800/80 dark:ring-white/15">
                @foreach ([MetricViewEnum::last_hour, MetricViewEnum::today, MetricViewEnum::week, MetricViewEnum::month] as $value)
                    <button type="button"
                            role="tab"
                            x-on:click="period = {{ $value->value }}"
                            x-bind:aria-selected="period === {{ $value->value }} ? 'true' : 'false'"
                            x-bind:class="period === {{ $value->value }}
                                ? 'bg-white text-zinc-900 shadow-sm ring-1 ring-zinc-900/10 dark:bg-zinc-700 dark:text-white dark:ring-white/15'
                                : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100'"
                            class="rounded-md px-2.5 py-1 text-xs font-medium transition">
                        {{ $value->getLabel() }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="relative -mx-1 h-56 sm:h-64 lg:h-72">
            <canvas x-ref="canvas" class="text-zinc-700 dark:text-zinc-200"></canvas>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('chart_{{ $metric->id }}', () => ({
            metric: {{ Js::from($metric) }},
            period: {{ Js::from($metric->default_view) }},
            points: [[], [], [], []],
            chart: null,
            init,
        }))
    })
</script>
