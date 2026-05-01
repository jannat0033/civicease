import forms from '@tailwindcss/forms';

export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './app/**/*.php',
  ],
  theme: {
    extend: {
      colors: {
        civic: {
          50: '#f4f8fb',
          100: '#e7f0f8',
          200: '#cfe0ef',
          500: '#2b6cb0',
          600: '#24578e',
          700: '#1f4a78',
          900: '#10283f',
        },
      },
      boxShadow: {
        soft: '0 10px 30px rgba(15, 23, 42, 0.08)',
      },
    },
  },
  plugins: [forms],
};
