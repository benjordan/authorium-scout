import defaultTheme from 'tailwindcss/defaultTheme';
import typographyPlugin from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            typography: {
                DEFAULT: {
                    css: {
                        color: '#333', // Default text color
                        h1: {
                            fontWeight: '700',
                            marginBottom: '0.5rem',
                        },
                        p: {
                            marginTop: '0.25rem',
                            marginBottom: '0.25rem',
                        },
                        ul: {
                            marginLeft: '1rem',
                            listStyleType: 'disc',
                        },
                        li: {
                            marginBottom: '0.25rem',
                        },
                        'ul > li::marker': {
                            color: '#4A5568', // Tailwind's gray-600
                        },
                        a: {
                            color: '#2563EB', // Tailwind's blue-600
                            textDecoration: 'underline',
                            '&:hover': {
                                color: '#1D4ED8', // Darker blue on hover
                            },
                        },
                        blockquote: {
                            borderLeftColor: '#D1D5DB', // Tailwind's gray-300
                            fontStyle: 'italic',
                        },
                    },
                },
            },
        },
    },
    plugins: [
        typographyPlugin,
    ],
};
