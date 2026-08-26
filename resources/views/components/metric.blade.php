@props([
    'metric',
])

@use('\Cachet\Enums\MetricViewEnum')
@use('\Cachet\Models\Metric')

@if ($metric instanceof Metric)
<div data-component="metric"
     data-metric-id="{{ $metric->getKey() }}"
     data-cachet-metric
     x-data="chart_{{ $metric->id }}"
     class="group relative overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-zinc-900/10 dark:bg-zinc-900 dark:ring-white/15">
    <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-accent/40 to-transparent" aria-hidden="true"></div>

    <div data-slot="content" class="flex flex-col gap-4 p-4 sm:gap-5 sm:p-6">
        <div data-slot="header" class="flex flex-wrap items-start justify-between gap-3">
            <div class="flex min-w-0 flex-col gap-1">
                @if($metric->component)
                    <div class="truncate text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                        {{ $metric->component->name }}
                    </div>
                @endif

                <div class="flex items-center gap-1.5">
                    <h3 data-slot="title" class="truncate text-lg font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">
                        {{ $metric->name }}
                    </h3>

                    @if($metric->description)
                        <div data-slot="description" x-data x-popover class="flex shrink-0 items-center">
                            <button type="button" x-ref="anchor" x-popover:button aria-label="{{ __('cachet::metric.description_label', ['metric' => $metric->name]) }}" class="flex size-10 items-center justify-center rounded-full text-zinc-400 transition hover:text-zinc-700 dark:text-zinc-500 dark:hover:text-zinc-200">
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
                    <div data-slot="suffix" class="text-xs font-medium text-zinc-500 dark:text-zinc-400">
                        {{ $metric->suffix }}
                    </div>
                @endif
            </div>

            <div data-slot="periods" role="tablist" aria-label="{{ __('cachet::metric.overview.metric_points_label') }}" class="inline-flex shrink-0 items-center gap-0.5 rounded-lg bg-zinc-100 p-0.5 ring-1 ring-zinc-900/10 dark:bg-zinc-800/80 dark:ring-white/15">
                @foreach ([MetricViewEnum::last_hour, MetricViewEnum::today, MetricViewEnum::week, MetricViewEnum::month] as $value)
                    <button data-slot="period"
                            type="button"
                            role="tab"
                            id="metric-{{ $metric->id }}-period-{{ $value->value }}"
                            aria-controls="metric-{{ $metric->id }}-chart"
                            x-on:click="period = {{ $value->value }}"
                            x-on:keydown.arrow-right.prevent="focusPeriod((period + 1) % 4)"
                            x-on:keydown.arrow-left.prevent="focusPeriod((period + 3) % 4)"
                            x-on:keydown.home.prevent="focusPeriod(0)"
                            x-on:keydown.end.prevent="focusPeriod(3)"
                            x-bind:aria-selected="period === {{ $value->value }} ? 'true' : 'false'"
                            x-bind:tabindex="period === {{ $value->value }} ? 0 : -1"
                            x-bind:class="period === {{ $value->value }}
                                ? 'bg-white text-zinc-900 shadow-sm ring-1 ring-zinc-900/10 dark:bg-zinc-700 dark:text-white dark:ring-white/15'
                                : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100'"
                            class="min-h-9 rounded-md px-2.5 py-1 text-xs font-medium transition">
                        {{ $value->getLabel() }}
                    </button>
                @endforeach
            </div>
        </div>

        <div data-slot="chart" id="metric-{{ $metric->id }}-chart" role="tabpanel" x-bind:aria-labelledby="'metric-{{ $metric->id }}-period-' + period" class="relative -mx-1 h-56 sm:h-64 lg:h-72">
            <canvas x-ref="canvas" role="img" aria-label="{{ __('cachet::metric.chart_label', ['metric' => $metric->name]) }}" class="text-zinc-700 dark:text-zinc-200"></canvas>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('chart_{{ $metric->id }}', () => ({
            metric: {{ Js::from($metric) }},
            metricView: {{ Js::from([
                'last_hour' => MetricViewEnum::last_hour->value,
                'today' => MetricViewEnum::today->value,
                'week' => MetricViewEnum::week->value,
                'month' => MetricViewEnum::month->value,
            ]) }},
            period: {{ Js::from($metric->default_view) }},
            points: [[], [], [], []],
            chart: null,
            focusPeriod(period) {
                this.period = period
                this.$nextTick(() => document.getElementById(`metric-{{ $metric->id }}-period-${period}`).focus())
            },
            init: window.cachetMetricChart,
        }))
    })
</script>
@endif
