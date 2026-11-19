/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./*.php",
    "./admin/*.php",
    "./includes/*.php",
    "./api/*.php",
    "./assets/js/*.js"
  ],
  theme: {
    extend: {
      colors: {
        'brand-dark': '#070b14',
        'brand-card': '#0e1626',
        'brand-red': '#dc2626',
        'brand-crimson': '#991b1b',
        'brand-gold': '#f59e0b'
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
        heading: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
        serif: ['Merriweather', 'serif']
      }
    }
  },
  plugins: []
};
