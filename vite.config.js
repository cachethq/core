import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
  build: {
    cssMinify: 'lightningcss'
  },
  plugins: [
    laravel({
      input: [
        'resources/css/cachet.css',
        'resources/css/dashboard/theme.css',
        'resources/js/cachet.js',
      ],
      // publicDirectory: 'vendor/orchestra/testbench-core/laravel/public/vendor/cachethq',
      // buildDirectory: 'cachet',
    }),
  ],
})
