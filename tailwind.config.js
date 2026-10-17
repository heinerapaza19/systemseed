import defaultTheme from 'tailwindcss/defaultTheme';

/**
 * Paleta SystemSeed (tomada del prototipo y del logo):
 *  - ink: fondos oscuros negro / navy / azul petróleo
 *  - lime / green: color principal
 *  - gold: acento (semilla)
 *  - teal / cyan: complementarios
 */
/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                seed: {
                    // Fondos (de más oscuro a más claro)
                    950: '#020d11',
                    900: '#031318',
                    850: '#04181d',
                    800: '#051d22',
                    700: '#08262c',
                    600: '#0a3035',
                    500: '#0f3d42',
                    // Marca
                    lime: '#b8ff39',
                    green: '#64ff58',
                    teal: '#18e2c3',
                    cyan: '#2be7ff',
                    gold: '#ffc83d',
                    amber: '#e89618',
                    // Texto
                    text: '#f6f9f8',
                    muted: '#a5b7b6',
                    dim: '#6d8584',
                    // Estado de error (solo para mensajes, nunca como color principal)
                    coral: '#ff8a7a',
                },
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                mono: ['"JetBrains Mono"', ...defaultTheme.fontFamily.mono],
            },
            boxShadow: {
                seed: '0 20px 60px rgba(0, 0, 0, .35)',
                card: '0 12px 30px rgba(0, 0, 0, .22)',
                glow: '0 0 24px rgba(184, 255, 57, .18)',
                'glow-cyan': '0 0 24px rgba(43, 231, 255, .16)',
                'glow-gold': '0 0 24px rgba(255, 200, 61, .22)',
            },
            backgroundImage: {
                'seed-primary': 'linear-gradient(90deg, #64ff58, #b8ff39)',
                'seed-cool': 'linear-gradient(90deg, #18e2c3, #2be7ff)',
                'seed-panel': 'linear-gradient(180deg, #08262c, #061a20)',
            },
            keyframes: {
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
                'spin-slow': {
                    to: { transform: 'rotate(360deg)' },
                },
                shimmer: {
                    '100%': { transform: 'translateX(100%)' },
                },
                'fade-up': {
                    from: { opacity: '0', transform: 'translateY(12px)' },
                    to: { opacity: '1', transform: 'translateY(0)' },
                },
            },
            animation: {
                float: 'float 6s ease-in-out infinite',
                'float-slow': 'float 9s ease-in-out infinite',
                'spin-slow': 'spin-slow 40s linear infinite',
                shimmer: 'shimmer 1.6s infinite',
                'fade-up': 'fade-up .5s ease-out both',
            },
        },
    },
    plugins: [],
};
