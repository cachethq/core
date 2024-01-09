import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/cachet.css', 'resources/js/cachet.js'],
            // publicDirectory: 'vendor/orchestra/testbench-core/laravel/public/vendor/cachethq',
            // buildDirectory: 'cachet',
        }),
    ],
})
