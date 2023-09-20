import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'

import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'

import '../css/cachet.css'

createInertiaApp({
  title: (title) => (title ? `${title} - Cachet` : 'Cachet'),
  resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue', { eager: true })),
  setup({ el, App, props, plugin }) {
    return createApp({
      mixins: [],
      render: () => h(App, props),
    })
      .use(plugin)
      .mount(el)
  },
})
