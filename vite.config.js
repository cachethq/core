import { defineConfig, splitVendorChunkPlugin } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [
    laravel([
      'resources/css/cachet.css',
      'resources/js/cachet.js',
    ]),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
    splitVendorChunkPlugin(),
  ],
  build: {
    rollupOptions: {
      external: [
        '@inertiajs/inertia-vue3',
      ],
    },
  },
})
