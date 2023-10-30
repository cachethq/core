import { defineConfig, splitVendorChunkPlugin } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/css/cachet.css',
        'resources/js/cachet.js',
      ],
      publicDirectory: 'vendor/orchestra/testbench-core/laravel/public/vendor/cachethq',
      buildDirectory: 'cachet',
    }),
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
