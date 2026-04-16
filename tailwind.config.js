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
                sans: ['Cairo', 'Tajawal', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    primary: '#1E3A8A',
                    accent: '#F59E0B',
                    background: '#F3F4F6',
                    text: '#0F172A',
                },
            },
        },
    },

    plugins: [forms],
};
