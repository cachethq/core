import defaultTheme from 'tailwindcss/defaultTheme'

/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'selector',
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './vendor/filament/**/*.blade.php',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['var(--font-family-sans)', 'ui-sans-serif', 'system-ui', 'sans-serif', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji'],
      },
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
      },
    },
  },
  plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
}
