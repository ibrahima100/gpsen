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
            colors: {
                gp: {
                    orange: '#F26B1D',
                    'orange-dark': '#C9510D',
                    ink: '#141414',
                    warm: '#5E5750',
                    sand: '#EFE3CC',
                    'sand-light': '#F8F2E6',
                    cream: '#FFFDF8',
                    border: '#D9C8A6',
                },
            },
            borderRadius: {
                gp: '12px',
            },
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                gp: '0 10px 30px -10px rgba(242, 107, 29, 0.35)',
            },
        },
    },

    plugins: [forms],
};
