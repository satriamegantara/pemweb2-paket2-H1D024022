import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: '#00bf63',
                    50: '#e6faf0',
                    100: '#cbf5e0',
                    200: '#98ebc2',
                    300: '#65e1a3',
                    400: '#33d785',
                    500: '#00bf63',
                    600: '#00994e',
                    700: '#00733a',
                    800: '#004d25',
                    900: '#002611',
                },
            },
        },
    },

    plugins: [forms],
};
