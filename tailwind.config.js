import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                'fantasque': ['Fantasque Sans Mono', 'monospace'],
            },
        },
    },
    plugins: [
        forms,
        typography,
        function ({ addBase, theme }) {
            addBase({
                '.scrollbar-thin': {
                    '&::-webkit-scrollbar': {
                        width: '8px',
                        height: '8px',
                    },
                    '&::-webkit-scrollbar-track': {
                        backgroundColor: theme('colors.slate.900'),
                    },
                    '&::-webkit-scrollbar-thumb': {
                        backgroundColor: theme('colors.slate.600'),
                        borderRadius: '4px',
                    },
                    'scrollbar-width': 'thin',
                    'scrollbar-color': `${theme('colors.slate.600')} ${theme('colors.slate.900')}`,
                },
            });
        },
    ],
};
