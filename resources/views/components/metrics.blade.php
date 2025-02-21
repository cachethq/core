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
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1,
                    },
                ],
            },
            options: {
                scales: {
                    x: {
                        type: 'timeseries',
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
    }
</script>

<div class="flex flex-col gap-8">
    @foreach ($metrics as $metric)
        <x-cachet::metric :metric="$metric" />
    @endforeach
</div>
