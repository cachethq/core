<script>
    const now = new Date()
    const previousHour = new Date(now - 60 * 60 * 1000)
    const previous24Hours = new Date(now - 24 * 60 * 60 * 1000)
    const previous7Days = new Date(now - 7 * 24 * 60 * 60 * 1000)
    const previous30Days = new Date(now - 30 * 24 * 60 * 60 * 1000)

    const MetricView = {{
        Js::from([
            'last_hour' => \Cachet\Enums\MetricViewEnum::last_hour->value,
            'today' => \Cachet\Enums\MetricViewEnum::today->value,
            'week' => \Cachet\Enums\MetricViewEnum::week->value,
            'month' => \Cachet\Enums\MetricViewEnum::month->value,
        ])
    }}

    function getCssVar(name) {
        return getComputedStyle(document.documentElement).getPropertyValue(name).trim()
    }

    function getFontColor() {
        if (window.matchMedia('(prefers-color-scheme: dark)').matches === true) {
            return `rgba(${getCssVar('--gray-100')}, 1)`
        }

        return `rgba(${getCssVar('--gray-800')}, 1)`
    }

    function getThemeColors() {
        const fontColor = getFontColor()
        const accent = `rgba(${getCssVar('--accent')}, 1)`
        const accentBackground = `rgba(${getCssVar('--accent-background')}, 0.2)`

        return {
            fontColor: fontColor,
            backgroundColors: [accent, accentBackground],
            borderColor: accent,
        }
    }

    let themeColors = getThemeColors()

    function init() {
        // Parse metric points
        const metricPoints = this.metric.metric_points.map((point) => {
            return {
                x: new Date(point.x),
                y: point.y,
            }
        })

        // Filter points based on the selected period
        this.points[0] = metricPoints.filter((point) => point.x >= previousHour)
        this.points[1] = metricPoints.filter((point) => point.x >= previous24Hours)
        this.points[2] = metricPoints.filter((point) => point.x >= previous7Days)
        this.points[3] = metricPoints.filter((point) => point.x >= previous30Days)

        // Initialize chart
        const chart = new Chart(this.$refs.canvas, {
            type: 'line',
            data: {
                datasets: [
                    {
                        label: this.metric.suffix,
                        data: this.points[this.period],
                        fill: false,
                        backgroundColor: themeColors.backgroundColors,
                        borderColor: themeColors.borderColor,
                        tension: 0.1,
                    },
                ],
            },
            options: {
                scales: {
                    x: {
                        ticks: {
                            color: themeColors.fontColor,
                        },
                        type: 'timeseries',
                    },
                    y: {
                        ticks: {
                            color: themeColors.fontColor,
                        },
                    },
                },
            },
        })

        this.$watch('period', () => {
            chart.data.datasets[0].data = this.points[this.period]
            chart.options.scales.x.time.unit = getTimeUnit(this.period)

            chart.update()
        })

        function getTimeUnit(period) {
            if (period == MetricView.last_hour) return 'minute'
            if (period == MetricView.today) return 'hour'
            if (period == MetricView.week) return 'week'
            if (period == MetricView.month) return 'month'
            return 'day'
        }

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            themeColors = getThemeColors()

            chart.data.datasets[0].backgroundColor = themeColors.backgroundColors
            chart.data.datasets[0].borderColor = themeColors.borderColor
            chart.options.plugins.legend.labels.color = themeColors.fontColor
            chart.options.plugins.tooltip.bodyColor = themeColors.fontColor
            chart.options.plugins.tooltip.titleColor = themeColors.fontColor
            chart.options.scales.x.ticks.color = themeColors.fontColor
            chart.options.scales.y.ticks.color = themeColors.fontColor

            chart.update()
        })
    }
</script>

<div class="flex flex-col gap-8">
    @foreach ($metrics as $metric)
        <x-cachet::metric :metric="$metric" />
    @endforeach
</div>
