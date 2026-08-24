import Chart from 'chart.js/auto'
import 'chartjs-adapter-moment'

Chart.defaults.color = '#fff'

const now = new Date()
const previousHour = new Date(now - 60 * 60 * 1000)
const previous24Hours = new Date(now - 24 * 60 * 60 * 1000)
const previous7Days = new Date(now - 7 * 24 * 60 * 60 * 1000)
const previous30Days = new Date(now - 30 * 24 * 60 * 60 * 1000)

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
    return color.replace(/\)\s*$/, ` / ${alpha})`)
}

function getThemeColors() {
    const fontColor = getFontColor()
    const accent = getCssVar('--accent')
    const isDark = window.matchMedia('(prefers-color-scheme: dark)').matches
    const gridColor = isDark ? 'rgba(255, 255, 255, 0.06)' : 'rgba(0, 0, 0, 0.05)'
    const mutedColor = isDark ? 'rgba(161, 161, 170, 1)' : 'rgba(113, 113, 122, 1)'

    return {
        fontColor,
        mutedColor,
        gridColor,
        fillColor: withAlpha(accent, 0.12),
        borderColor: accent,
    }
}

function getTimeUnit(period, metricView) {
    if (period === metricView.last_hour) return 'minute'
    if (period === metricView.today) return 'hour'
    if (period === metricView.week) return 'week'
    if (period === metricView.month) return 'month'

    return 'day'
}

window.cachetMetricChart = function () {
    const rawPoints = (this.metric.chart_points?.raw ?? []).map((point) => ({
        x: new Date(point.x),
        y: point.y,
    }))
    const hourlyPoints = (this.metric.chart_points?.hourly ?? []).map((point) => ({
        x: new Date(point.x),
        y: point.y,
    }))

    this.points[0] = rawPoints.filter((point) => point.x >= previousHour)
    this.points[1] = rawPoints.filter((point) => point.x >= previous24Hours)
    this.points[2] = hourlyPoints.filter((point) => point.x >= previous7Days)
    this.points[3] = hourlyPoints.filter((point) => point.x >= previous30Days)

    let themeColors = getThemeColors()
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
            interaction: { intersect: false, mode: 'index' },
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
                    ticks: { color: themeColors.mutedColor, font: { size: 11 }, maxRotation: 0, autoSkipPadding: 16 },
                },
                y: {
                    border: { display: false },
                    grid: { color: themeColors.gridColor, drawTicks: false },
                    ticks: { color: themeColors.mutedColor, font: { size: 11 }, padding: 8, maxTicksLimit: 5 },
                },
            },
        },
    })

    this.$watch('period', () => {
        chart.data.datasets[0].data = this.points[this.period]
        chart.options.scales.x.time.unit = getTimeUnit(this.period, this.metricView)
        chart.update()
    })

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
