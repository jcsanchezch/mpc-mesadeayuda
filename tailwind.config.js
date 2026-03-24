/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: [
          'Inter',
          'Instrument Sans',
          'ui-sans-serif',
          'system-ui',
          'sans-serif',
          'Apple Color Emoji',
          'Segoe UI Emoji',
          'Segoe UI Symbol',
          'Noto Color Emoji'
        ],
      },
    },
  },
  safelist: [
    // Colores de estados (valores desde BD, no detectables por el purger)
    'text-yellow-700',  'bg-yellow-200',  'bg-yellow-100',
    'text-purple-700',  'bg-purple-200', 'bg-purple-100',
    'text-indigo-700',  'bg-indigo-200', 'bg-indigo-100',
    'text-blue-700',    'bg-blue-200', 'bg-blue-100',
    'text-orange-700',  'bg-orange-200', 'bg-orange-100',
    'text-emerald-700', 'bg-emerald-200', 'bg-emerald-100',
    'text-red-700',     'bg-red-200',  'bg-red-100',
    'text-gray-500',    'bg-gray-200', 'bg-gray-100',
  ],
  plugins: [],
}
