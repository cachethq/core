import { cpSync, existsSync, rmSync } from 'node:fs'
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import tailwindcss from "@tailwindcss/vite"

/**
 * Mirror the compiled assets into the Testbench skeleton after each build.
 */
function syncTestbenchAssets() {
  const source = 'public/build'
  const target = 'vendor/orchestra/testbench-core/laravel/public/vendor/cachethq/cachet/build'

  return {
    name: 'cachet-sync-testbench-assets',
    apply: 'build',
    closeBundle() {
      if (!existsSync(`${target}/..`)) {
        return
      }

      rmSync(target, { recursive: true, force: true })
      cpSync(source, target, { recursive: true })
    },
  }
}

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/css/cachet.css',
        'resources/css/dashboard/theme.css',
        'resources/js/cachet.js',
      ],
    }),
    tailwindcss(),
    syncTestbenchAssets(),
  ],
})
