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
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Ana renkler
                primary: {
                    100: '#E8F0FE',
                    500: '#3B82F6',
                    600: '#2563EB',
                },
                accent: {
                    100: '#F3E8FF',
                    500: '#8B5CF6',
                    600: '#7C3AED',
                },
                success: {
                    100: '#DCFCE7',
                    500: '#10B981',
                    600: '#059669',
                },
                warning: {
                    100: '#FEF3C7',
                    500: '#F59E0B',
                    600: '#D97706',
                },
                danger: {
                    100: '#FEE2E2',
                    500: '#EF4444',
                    600: '#DC2626',
                },
                // Nötr renkler
                page: '#F8FAFC',
                surface: '#FFFFFF',
                muted: '#F1F5F9',
                border: '#E2E8F0',
                // Metin renkleri
                text: {
                    primary: '#0F172A',
                    secondary: '#334155',
                    muted: '#64748B',
                },
            },
            spacing: {
                '18': '4.5rem',
                '88': '22rem',
            },
            borderRadius: {
                'sm': '8px',
                'md': '12px',
                'lg': '16px',
                'pill': '999px',
            },
            boxShadow: {
                'elev-0': 'none',
                'elev-1': '0 1px 2px rgba(0,0,0,.06)',
                'elev-2': '0 4px 8px rgba(0,0,0,.07)',
                'elev-3': '0 10px 16px rgba(0,0,0,.10)',
            },
            fontSize: {
                'xs': ['12px', { lineHeight: '18px' }],
                'sm': ['14px', { lineHeight: '22px' }],
                'base': ['16px', { lineHeight: '24px' }],
                'lg': ['18px', { lineHeight: '28px' }],
                'xl': ['20px', { lineHeight: '28px' }],
                '2xl': ['24px', { lineHeight: '32px' }],
                '3xl': ['28px', { lineHeight: '36px' }],
            },
            letterSpacing: {
                'wide': '0.2px',
            },
        },
    },

    plugins: [forms],
};
