import Chart from 'chart.js/auto'
import 'chartjs-adapter-moment'

import Alpine from 'alpinejs'

import Anchor from '@alpinejs/anchor'
import Collapse from '@alpinejs/collapse'
import Focus from '@alpinejs/focus'
import Ui from '@alpinejs/ui'

Chart.defaults.color = '#fff'
window.Chart = Chart

Alpine.plugin(Anchor)
Alpine.plugin(Collapse)
Alpine.plugin(Focus)
Alpine.plugin(Ui)

const prefersDark = window.matchMedia('(prefers-color-scheme: dark)')

Alpine.store('theme', {
    mode: 'auto',
    forced: false,

    init() {
        this.forced = ['light', 'dark'].includes(document.documentElement.dataset.themeMode)

        if (this.forced) {
            return
        }

        let stored = null
        try {
            stored = localStorage.getItem('cachet.theme')
        } catch {}
        this.mode = ['light', 'dark', 'auto'].includes(stored) ? stored : 'auto'

        prefersDark.addEventListener('change', () => {
            if (this.mode === 'auto') {
                this.apply()
            }
        })

        this.apply()
    },

    get isDark() {
        return this.mode === 'dark' || (this.mode === 'auto' && prefersDark.matches)
    },

    select(mode) {
        if (this.forced) {
            return
        }

        this.mode = mode
        try {
            localStorage.setItem('cachet.theme', mode)
        } catch {}
        this.apply()
    },

    apply() {
        document.documentElement.classList.toggle('dark', this.isDark)
        document.documentElement.classList.toggle('light', !this.isDark)
        window.dispatchEvent(new CustomEvent('theme-changed', { detail: { dark: this.isDark } }))
    },
})

window.Alpine = Alpine
Alpine.start()
