import preset from '../../../vendor/filament/filament/tailwind.config.preset'

export default {
  presets: [preset],
  content: [
    './resources/views/filament/**/*.blade.php',
    './vendor/filament/**/*.blade.php',
  ],
}
