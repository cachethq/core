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

window.Alpine = Alpine
Alpine.start()
