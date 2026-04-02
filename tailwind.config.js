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
                sans: ['Bricolage Grotesque', ...defaultTheme.fontFamily.sans],
                mono: ['Google Sans Code', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                surface: {DEFAULT: '#ffffff', 2: '#f9f8f5', 3: '#f0efe9'},
                canvas: '#f5f4f0',
                edge: {DEFAULT: '#e2e0d8', 2: '#ccc9be'},
                ink: {DEFAULT: '#1a1916', 2: '#6b6860', 3: '#9b9890'},
                finance: {
                    green: '#166534', 'green-bg': '#f0fdf4', 'green-border': '#bbf7d0',
                    red: '#991b1b', 'red-bg': '#fef2f2', 'red-border': '#fecaca',
                    amber: '#92400e', 'amber-bg': '#fffbeb', 'amber-border': '#fde68a',
                    blue: '#1e3a5f', 'blue-bg': '#eff6ff', 'blue-border': '#bfdbfe',
                },
            },
        }, borderWidth: {DEFAULT: '0.5px'},
    },
    plugins: [forms],
};
