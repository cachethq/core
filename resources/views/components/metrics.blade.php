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

    function withAlpha(color, alpha) {
        // Inject `/ alpha` into a CSS color function (oklch/rgb/hsl/...) so it works regardless of the theme's color space.
        return color.replace(/\)\s*$/, ` / ${alpha})`)
    }

    function getThemeColors() {
        const fontColor = getFontColor()
        const accent = getCssVar('--accent')
        const isDark = window.matchMedia('(prefers-color-scheme: dark)').matches
        const gridColor = isDark ? 'rgba(255, 255, 255, 0.06)' : 'rgba(0, 0, 0, 0.05)'
        const mutedColor = isDark ? 'rgba(161, 161, 170, 1)' : 'rgba(113, 113, 122, 1)'

        return {
            fontColor: fontColor,
            mutedColor: mutedColor,
            gridColor: gridColor,
            fillColor: withAlpha(accent, 0.12),
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
                        fill: true,
                        backgroundColor: themeColors.fillColor,
                        borderColor: themeColors.borderColor,
                        borderWidth: 2,
                        tension: 0.35,
                        pointRadius: 0,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: themeColors.borderColor,
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 2,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(24, 24, 27, 0.95)',
                        titleColor: '#fafafa',
                        bodyColor: '#fafafa',
                        borderColor: 'rgba(255, 255, 255, 0.08)',
                        borderWidth: 1,
                        padding: 10,
                        cornerRadius: 6,
                        displayColors: false,
                        titleFont: { weight: '500', size: 11 },
                        bodyFont: { weight: '600', size: 12 },
                    },
                },
                scales: {
                    x: {
                        type: 'timeseries',
                        border: { display: false },
                        grid: { display: false },
                        ticks: {
                            color: themeColors.mutedColor,
                            font: { size: 11 },
                            maxRotation: 0,
                            autoSkipPadding: 16,
                        },
                    },
                    y: {
                        border: { display: false },
                        grid: {
                            color: themeColors.gridColor,
                            drawTicks: false,
                        },
                        ticks: {
                            color: themeColors.mutedColor,
                            font: { size: 11 },
                            padding: 8,
                            maxTicksLimit: 5,
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

            chart.data.datasets[0].backgroundColor = themeColors.fillColor
            chart.data.datasets[0].borderColor = themeColors.borderColor
            chart.data.datasets[0].pointHoverBackgroundColor = themeColors.borderColor
            chart.options.scales.x.ticks.color = themeColors.mutedColor
            chart.options.scales.y.ticks.color = themeColors.mutedColor
            chart.options.scales.y.grid.color = themeColors.gridColor

            chart.update()
        })
    }
</script>

<div class="flex flex-col gap-8">
    @foreach ($metrics as $metric)
        <x-cachet::metric :metric="$metric" />
    @endforeach
</div>
