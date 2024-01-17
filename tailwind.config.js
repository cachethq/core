const defaultTheme = require('tailwindcss/defaultTheme')

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          '50': '#e6f7ed',
          '100': '#c1edce',
          '200': '#9be3b1',
          '300': '#74d994',
          '400': '#4dce76',
          '500': '#04c147', // Base Color
          '600': '#03ad40',
          '700': '#038f34',
          '800': '#027227',
          '900': '#01561a',
        },
        custom: {
          50: 'rgba(var(--c-50), <alpha-value>)',
          100: 'rgba(var(--c-100), <alpha-value>)',
          200: 'rgba(var(--c-200), <alpha-value>)',
          300: 'rgba(var(--c-300), <alpha-value>)',
          400: 'rgba(var(--c-400), <alpha-value>)',
          500: 'rgba(var(--c-500), <alpha-value>)',
          600: 'rgba(var(--c-600), <alpha-value>)',
          700: 'rgba(var(--c-700), <alpha-value>)',
          800: 'rgba(var(--c-800), <alpha-value>)',
          900: 'rgba(var(--c-900), <alpha-value>)',
          950: 'rgba(var(--c-950), <alpha-value>)',
        },
        background: {
          light: 'var(--background-light)',
          dark: 'var(--background-dark)',
        },
        base: {
          light: 'var(--text-light)',
          dark: 'var(--text-dark)',
        },
        divider: {
          light: 'var(--divider-light)',
          dark: 'var(--divider-dark)',
        },
        panel: {
          light: 'var(--panel-light)',
          dark: 'var(--panel-dark)',
        },
        'panel-header': {
          light: 'var(--panel-header-light)',
          dark: 'var(--panel-header-dark)',
        },
      },
    },
  },
  plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
}
