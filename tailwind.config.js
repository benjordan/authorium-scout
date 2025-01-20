import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

const plugin = require('tailwindcss/plugin');

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            colors: {
                brand: {
                    50: "#ECF9F3",
                    100: "#D9F2E7",
                    200: "#AEE5CC",
                    300: "#88D8B4",
                    400: "#62CB9C",
                    500: "#3DBA81",
                    600: "#319668",
                    700: "#256F4E",
                    800: "#184933",
                    900: "#0D261B",
                    950: "#06130D"
                },
            },
        },
    },

    plugins: [
        forms,
        typography, // Add this line for the `prose` class
        plugin(function({ addComponents }) {
            addComponents({
                '.text-content': {
                    '@apply prose max-w-none': {},

                    'p': {
                        '@apply my-4 text-gray-700': {},
                    },
                    'ul': {
                        '@apply list-disc pl-5 my-4 space-y-2 text-gray-700': {},
                    },
                    'ol': {
                        '@apply list-decimal pl-5 my-4 space-y-2 text-gray-700': {},
                    },
                    'li': {
                        '@apply text-gray-700 leading-relaxed': {},
                    },
                    'blockquote': {
                        '@apply pl-4 border-l-4 border-brand-500 italic text-gray-500 my-4': {},
                    },
                    'hr': {
                        '@apply my-6 border-gray-200': {},
                    },
                    'a': {
                        '@apply text-brand-600 hover:underline': {},
                    },
                    'h1': {
                        '@apply text-3xl font-bold text-gray-800 mt-8 mb-4': {},
                    },
                    'h2': {
                        '@apply text-2xl font-bold text-gray-700 mt-6 mb-3': {},
                    },
                    'h3': {
                        '@apply text-xl font-semibold text-gray-600 mt-4 mb-2': {},
                    },
                    'h4, h5, h6': {
                        '@apply text-lg font-medium text-gray-500 mt-3 mb-2': {},
                    },
                    'pre': {
                        '@apply bg-gray-100 p-4 rounded text-sm overflow-auto my-4': {},
                    },
                    'code': {
                        '@apply bg-gray-100 px-1 py-0.5 rounded text-sm text-red-600': {},
                    },
                },
            });
        }),
    ],

    safelist: [
        'bg-amber-500', // Missing Information
        'bg-sky-500',    // PM Analysis, Requirement Writing
        'bg-purple-500', // In Design
        'bg-gray-500',   // Icebox, Incoming, Unknown
        'bg-cyan-500',   // Ready for Dev
        'bg-teal-500',   // Pending CR, In QA, Dev QA
        'bg-blue-500',   // In Development
        'bg-yellow-500', // QA Review
        'bg-brand-500', // Ready for Release
        'bg-red-500',     // Blocked, Pushed Back
    ],
};
