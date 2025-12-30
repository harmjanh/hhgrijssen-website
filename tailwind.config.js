import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: '#f5f5f0',
                    100: '#E5E5CD',
                    200: '#d4d4b8',
                    300: '#c3c3a3',
                    400: '#b2b28e',
                    500: '#E5E5CD',
                    600: '#9a9a7a',
                    700: '#7a7a62',
                    800: '#5a5a4a',
                    900: '#3a3a32',
                },
            },
        },
    },

    plugins: [forms],
};
