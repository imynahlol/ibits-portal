/* Tailwind CDN configuration — must load AFTER the Tailwind CDN script. */
tailwind.config = {
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                primary:       '#06215c',
                primaryLight:  '#0a328c',
                secondary:     '#fad517',
                secondaryHover:'#e5c215',
                background:    '#ffffff',
                soft:          '#f8fafc',
                surface:       '#ffffff',
                textDark:      '#1f2937',
                textMuted:     '#6b7280',
            },
            fontFamily: {
                sans: ['Poppins', 'sans-serif'],
            },
            animation: {
                'fade-in':   'fadeIn 0.5s ease-out forwards',
                'slide-up':  'slideUp 0.6s ease-out forwards',
                'pulse-slow':'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%':   { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%':   { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
        },
    },
};